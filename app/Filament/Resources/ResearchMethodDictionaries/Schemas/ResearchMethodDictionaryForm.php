<?php

namespace App\Filament\Resources\ResearchMethodDictionaries\Schemas;

use Closure;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ResearchMethodDictionaryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('method_name')
                ->label('Nama metode')
                ->placeholder('Contoh: Rapid Application Development (RAD)')
                ->helperText('Nama baku yang akan disimpan pada publikasi ketika istilah ini terdeteksi.')
                ->required()
                ->maxLength(255),
            TagsInput::make('aliases')
                ->label('Alias atau variasi istilah')
                ->placeholder('Ketik istilah, lalu tekan Enter')
                ->helperText('Masukkan variasi yang mungkin muncul di judul, keyword, atau abstrak. Hindari kata umum seperti sistem, data, atau metode.')
                ->required()
                ->rules([
                    'array',
                    'min:1',
                    function (string $attribute, mixed $value, Closure $fail): void {
                        $genericTerms = [
                            'data',
                            'informasi',
                            'metode',
                            'penelitian',
                            'sistem',
                            'website',
                        ];

                        foreach ((array) $value as $alias) {
                            if (in_array(strtolower(trim((string) $alias)), $genericTerms, true)) {
                                $fail('Alias terlalu umum dan dapat menyebabkan false positive. Gunakan istilah yang lebih spesifik.');
                            }
                        }
                    },
                ])
                ->nestedRecursiveRules(['string', 'min:2']),
            Textarea::make('description')
                ->label('Keterangan')
                ->placeholder('Jelaskan peran metode ini agar admin lain tidak salah mengelompokkan istilahnya.')
                ->helperText('Keterangan hanya untuk dokumentasi admin; tidak ikut dihitung oleh detektor.')
                ->rows(3),
            Select::make('category')
                ->label('Kategori')
                ->helperText('Pilih peran istilah: pendekatan penelitian, pengembangan sistem, pengujian, analisis, atau teknologi pendukung.')
                ->options([
                    'research' => 'Pendekatan penelitian',
                    'development' => 'Pengembangan sistem',
                    'testing' => 'Pengujian',
                    'analysis' => 'Analisis atau pengambilan keputusan',
                    'technology' => 'Teknologi pendukung',
                ])
                ->default('research')
                ->required(),
            TextInput::make('priority')
                ->label('Prioritas')
                ->helperText('Skala relatif 0-1000, bukan persentase. Nilai lebih tinggi lebih diutamakan saat beberapa istilah cocok.')
                ->numeric()
                ->default(100)
                ->minValue(0)
                ->maxValue(1000)
                ->required(),
            Toggle::make('is_active')
                ->label('Aktif digunakan')
                ->helperText('Matikan jika istilah ini sering menghasilkan deteksi yang tidak sesuai. Data tetap tersimpan.')
                ->default(true),
        ]);
    }
}
