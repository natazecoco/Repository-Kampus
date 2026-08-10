<?php

namespace App\Filament\Resources\Publications\Schemas;

use App\Models\Publication;
use App\Services\ResearchMethodDetector;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class PublicationForm
{
    public static function configure(Schema $schema): Schema
    {
        $detectResearchMethod = function (Get $get, Set $set) {
            $title = (string) ($get('title') ?? '');
            $keywordsData = $get('keywords');
            $keywordsString = is_array($keywordsData) ? implode(' ', $keywordsData) : (string) ($keywordsData ?? '');
            $abstract = (string) ($get('abstract') ?? '');

            $detector = app(ResearchMethodDetector::class);
            $detectedMethod = $detector->detect($title, $keywordsString, $abstract);

            if ($detectedMethod && empty($get('research_method'))) {
                $set('research_method', $detectedMethod);
            }
        };

        return $schema
            ->components([
                Select::make('container_id')
                    ->relationship('container', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('type')
                    ->options(Publication::TYPE_LABELS)
                    ->default('thesis')
                    ->live()
                    ->afterStateUpdated(function (Get $get, Set $set, ?string $state): void {
                        if (filled($get('files'))) {
                            return;
                        }

                        $set('files', static::fileTemplate($state));
                    })
                    ->helperText('Jenis karya menentukan template berkas. Template hanya diterapkan pada berkas yang masih kosong.')
                    ->required(),

                Placeholder::make('required_sections_check')
                    ->label('Pemeriksaan bagian wajib')
                    ->content(function (Get $get) {
                        $type = $get('type') ?? 'thesis';
                        $files = $get('files') ?? [];

                        $present = array_map(fn($f) => $f['section'] ?? null, $files);
                        $required = Publication::requiredSectionsForType($type);
                        $missing = array_values(array_diff($required, $present));
                        $countMissing = count($missing);

                        if ($countMissing === 0) {
                            return '<span style="color:green">✓ Semua bagian wajib ada menurut template saat ini.</span>';
                        }

                        $labels = array_map(fn($k) => static::sectionOptions()[$k] ?? $k, $missing);
                        $list = implode(', ', $labels);

                        return new HtmlString("<strong style=\"color:#b45309\">⚠ {$countMissing} bagian wajib belum ada:</strong> {$list}.<br><br>Silakan tambahkan bagian-bagian ini sebelum menyimpan jika kamu ingin memastikan kelengkapan publikasi.");
                    })
                    ->reactive(),

                TextInput::make('title')
                    ->required()
                    ->maxLength(255),

                TextInput::make('author')
                    ->required()
                    ->maxLength(255),

                TextInput::make('year')
                    ->numeric()
                    ->required(),

                Textarea::make('abstract')
                    ->rows(5)
                    ->required(fn (Get $get) => $get('type') !== 'book') // Tidak wajib khusus untuk Buku
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Get $get, Set $set) use ($detectResearchMethod) {
                        $detectResearchMethod($get, $set);
                    }),

                TagsInput::make('keywords')
                    ->label('Kata Kunci')
                    ->separator(',')
                    ->splitKeys(['Tab', 'Enter'])
                    ->helperText('Tekan Enter atau Tab setelah setiap kata kunci. Tag ini digunakan oleh pencarian dan rekomendasi dokumen.')
                    ->required(fn (Get $get) => $get('type') !== 'book') // Tidak wajib khusus untuk Buku
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Get $get, Set $set) use ($detectResearchMethod) {
                        $detectResearchMethod($get, $set);
                    }),

                Select::make('topics')
                    ->label('Topik Repository')
                    ->relationship('topics', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->required(fn (Get $get) => $get('type') === 'book') // WAJIB khusus untuk Buku agar tidak jadi "hantu"
                    ->createOptionForm([
                        \Filament\Forms\Components\TextInput::make('name')
                            ->label('Nama topik')
                            ->required()
                            ->afterStateUpdated(fn ($state, $set) => $set('slug', \Illuminate\Support\Str::slug($state))),
                        \Filament\Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->required(),
                    ])
                    ->helperText('Pilih topik terstruktur untuk mendukung pencarian dan rekomendasi bacaan pelengkap. Ketik untuk mencari, atau buat topik baru.'),

                Select::make('language')
                    ->label('Bahasa')
                    ->options([
                        'id' => 'Indonesia',
                        'en' => 'Inggris',
                    ])
                    ->default('id')
                    ->searchable(),

                TextInput::make('research_method')
                    ->label('Metode Riset')
                    ->placeholder('Contoh: Kualitatif, Kuantitatif, SDLC Waterfall...')
                    ->maxLength(255)
                    ->helperText('Otomatis terdeteksi dari Kata Kunci atau Abstrak. Dapat diubah manual jika tidak sesuai.'),

                Select::make('license')
                    ->label('Lisensi')
                    ->options([
                        'Internal Use Only' => 'Internal Use Only (Khusus Kampus)',
                        'All Rights Reserved' => 'All Rights Reserved (Hak Cipta Penuh)',
                        'CC BY' => 'CC BY (Creative Commons - Atribusi)',
                        'CC BY-NC' => 'CC BY-NC (Non-Komersial)',
                    ])
                    ->default('Internal Use Only')
                    ->searchable(),

                TextInput::make('doi')
                    ->label('DOI (Digital Object Identifier)')
                    ->placeholder('Contoh: 10.1000/xyz123')
                    ->maxLength(255)
                    ->helperText('Kosongkan jika publikasi belum memiliki DOI resmi.'),

                Repeater::make('files')
                    ->label('Berkas Publikasi')
                    ->relationship('files')
                    ->default(fn (Get $get): array => static::fileTemplate($get('type') ?? 'thesis'))
                    ->schema([
                        Select::make('section')
                            ->label('Bagian')
                            ->options(static::sectionOptions())
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(function (Set $set, ?string $state): void {
                                if (filled($state)) {
                                    $set('title', static::sectionOptions()[$state] ?? $state);
                                }
                            })
                            ->required(),

                        TextInput::make('title')
                            ->label('Nama Bagian')
                            ->placeholder('Contoh: Bab I – Pendahuluan')
                            ->required()
                            ->maxLength(255),

                        Select::make('visibility')
                            ->label('Siapa yang dapat membaca')
                            ->options([
                                'public' => 'Publik',
                                'authenticated' => 'Mahasiswa internal (wajib login)',
                                'admin' => 'Admin saja',
                            ])
                            ->default('authenticated')
                            ->required(),

                        Toggle::make('allow_download')
                            ->label('Izinkan unduh PDF')
                            ->helperText('Aktifkan hanya untuk dokumen yang memang berlisensi atau berizin untuk diunduh.')
                            ->default(false),

                        FileUpload::make('file_path')
                            ->label('File PDF')
                            ->disk('local')
                            ->visibility('private')
                            ->acceptedFileTypes(['application/pdf'])
                            ->directory('publications_pdf')
                            ->maxSize(10240)
                            ->required(),
                    ])
                    ->columns(2)
                    ->orderColumn('sort_order')
                    ->defaultItems(1)
                    ->addActionLabel('Tambah berkas atau bagian lain')
                    ->helperText('Karya akademik menggunakan template per bagian. Artikel dan buku dapat diunggah sebagai satu dokumen lengkap.'),
            ]);
    }

    public static function sectionOptions(): array
    {
        return [
            'full_document' => 'Dokumen lengkap',
            'cover' => 'Cover Penulisan',
            'originality_statement' => 'Pernyataan Orisinalitas dan Publikasi',
            'approval_sheet' => 'Lembar Pengesahan',
            'abstract_id' => 'Abstrak Bahasa Indonesia',
            'abstract_en' => 'Abstract Bahasa Inggris',
            'preface' => 'Kata Pengantar',
            'table_of_contents' => 'Daftar Isi',
            'list_of_tables' => 'Daftar Tabel',
            'list_of_figures' => 'Daftar Gambar',
            'list_of_formulas' => 'Daftar Rumus',
            'list_of_appendices' => 'Daftar Lampiran',
            'chapter_1' => 'Bab I – Pendahuluan',
            'chapter_2' => 'Bab II – Tinjauan Pustaka',
            'chapter_3' => 'Bab III – Metodologi',
            'chapter_4' => 'Bab IV – Hasil dan Pembahasan',
            'chapter_5' => 'Bab V – Kesimpulan',
            'chapter_6' => 'Bab VI',
            'bibliography' => 'Daftar Pustaka',
            'appendix' => 'Lampiran',
            'application_test_statement' => 'Pernyataan Uji Coba Aplikasi',
            'program_listing' => 'Listing Program',
            'program_output' => 'Output Program',
            'presentation' => 'Dokumen Presentasi Sidang',
            'other' => 'Berkas Lainnya',
        ];
    }

    private static function fileTemplate(?string $type): array
    {
        if (in_array($type, ['article', 'book', 'proceeding', 'report'], true)) {
            return [[
                'section' => 'full_document',
                'title' => 'Dokumen lengkap',
                'visibility' => 'public',
                'allow_download' => false,
            ]];
        }

        $sections = [
            ['cover', 'public'],
            ['originality_statement', 'admin'],
            ['approval_sheet', 'admin'],
            ['abstract_id', 'public'],
            ['abstract_en', 'public'],
            ['preface', 'authenticated'],
            ['table_of_contents', 'authenticated'],
            ['list_of_tables', 'authenticated'],
            ['list_of_figures', 'authenticated'],
            ['list_of_formulas', 'authenticated'],
            ['list_of_appendices', 'authenticated'],
            ['chapter_1', 'authenticated'],
            ['chapter_2', 'authenticated'],
            ['chapter_3', 'authenticated'],
            ['chapter_4', 'authenticated'],
            ['chapter_5', 'authenticated'],
            ['bibliography', 'authenticated'],
            ['appendix', 'admin'],
        ];

        return array_map(fn (array $section): array => [
            'section' => $section[0],
            'title' => static::sectionOptions()[$section[0]],
            'visibility' => $section[1],
            'allow_download' => false,
        ], $sections);
    }
}