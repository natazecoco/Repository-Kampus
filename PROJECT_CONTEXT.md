# Project Context for AI Assistants

## Overview
This repository is a Laravel-based academic repository system for Universitas Gunadarma. It manages scientific publications, research documents, topic taxonomy, student authentication, bookmarks, and an admin panel powered by Filament.

The system is designed to feel like a modern digital repository with smart recommendation features, not just a simple CRUD app. The recommendation experience is based on metadata, topic overlap, keyword overlap, research method similarity, recency, popularity, and user interest signals.

## Tech Stack
- PHP 8.3
- Laravel 13.x
- Filament 5.x for admin panel
- Blade + Tailwind CSS for frontend
- MySQL database
- PHPUnit for testing
- Sastrawi for Indonesian text preprocessing

## Main Goals
- Store and browse academic publications such as theses, scientific papers, journals, books, proceedings, and reports.
- Provide semantic-style search and topic-based filtering.
- Recommend related publications to users based on content and context.
- Allow authenticated students to bookmark publications and mark topic preferences.
- Give admins a clean dashboard to manage repository content and inspect repository activity.

## Core Domain Models
### Publication
The central model for academic documents.
Key properties typically include:
- title
- author
- abstract
- keywords
- year
- type
- research_method
- views_count
- container_id

Related models:
- PublicationFile
- Container
- Topic
- Recommendation
- Bookmark

### Topic
Represents the subject/category taxonomy used for browsing and recommendation.
Topics can be hierarchical with parent/child structure.

### TopicDictionary
Used to support semantic keyword expansion and title normalization logic. This is relevant for better search and auto-tagging behavior.

### PublicationFile
Represents files attached to a publication such as cover pages, abstracts, chapter files, and supplementary documents.
Files can have different access levels (public, authenticated, restricted, etc.).

### Recommendation
Stores the result of recommendation scoring for a publication and its recommended publications.

### User
Users can be:
- student
- admin

Students can bookmark publications and set topic preferences. Admin users can access the Filament panel.

## Key Features
### Public Catalog / Repository UI
The homepage shows:
- search box
- topic cards
- filter controls by type, method, and year
- publication listing cards
- insight cards and repository summary

### Publication Detail Page
Each publication page displays:
- metadata (title, author, year, type, method)
- abstract
- keywords and topic tags
- file list with access status
- related recommendations

### Recommendation Engine
Recommendations are not just based on one signal. The scoring logic combines:
- text similarity
- topic overlap / topic context overlap
- keyword overlap
- research method similarity
- document type similarity
- recency
- popularity (views/downloads/bookmarks)
- user preference and bookmark behavior

The logic is implemented in:
- app/Services/RecommendationScorer.php
- app/Services/ResearchMethodDetector.php
- app/Jobs/GenerateRecommendations.php

### Bookmarking and Personalization
Authenticated users can:
- bookmark publications
- mark topic preferences
- receive more personalized recommendations based on their activity

### Admin Panel
Filament is used to manage the repository from the admin area.
Relevant admin resources include:
- publications
- topics
- containers
- topic dictionaries
- users

Admin widgets include:
- dashboard insight stats
- analytics charts

## Main Application Structure
### app/
Core application code.
Important folders:
- app/Http/Controllers/ - controllers for homepage, publication detail, auth, bookmarks, document access
- app/Models/ - Eloquent models for repository entities
- app/Services/ - recommendation logic, text processing, and supporting services
- app/Jobs/ - background jobs, especially recommendation generation
- app/Filament/ - admin resources and widgets

### routes/
Main web routes are defined in routes/web.php.
Key route groups include:
- public catalog routes
- publication detail routes
- student login/register routes
- bookmark routes
- admin panel routes

### resources/views/
Blade templates for the user-facing UI.
Important views include:
- resources/views/index.blade.php
- resources/views/show.blade.php
- resources/views/partials/publication-item.blade.php

### database/
Contains migrations, seeders, and factories.
Important for the schema of:
- publications
- topics
- publication_files
- recommendations
- bookmarks
- user topic preferences

### tests/
Includes feature and unit tests. The current regression suite can be run with:
- php artisan test --filter ExampleTest

## Important Files
These files are especially relevant to understand the project quickly:
- composer.json
- routes/web.php
- app/Http/Controllers/PublicationController.php
- app/Http/Controllers/Auth/StudentAuthController.php
- app/Http/Controllers/BookmarkController.php
- app/Models/Publication.php
- app/Models/Topic.php
- app/Models/User.php
- app/Models/Recommendation.php
- app/Services/RecommendationScorer.php
- app/Jobs/GenerateRecommendations.php
- app/Filament/Resources/Publications/PublicationResource.php
- app/Filament/Resources/Topics/TopicResource.php
- app/Filament/Resources/TopicDictionaries/TopicDictionaryResource.php
- app/Filament/Resources/ResearchMethodDictionaries/ResearchMethodDictionaryResource.php
- app/Filament/Widgets/AdminInsightWidget.php
- app/Filament/Widgets/AdminAnalyticsWidget.php
- resources/views/index.blade.php
- resources/views/show.blade.php

## Important Routes
- / : homepage/catalog
- /publication/{publication} : publication detail page
- /publication/{publication}/read : viewer for publication files
- /publication/{publication}/file/{file}/download : file download route
- /bookmarks : bookmarked publications page
- /student/login : student login page
- /student/register : student registration page
- /dashboard : student dashboard
- /admin : Filament admin panel

## Authentication and Access Model
There are two main user contexts:
- Guest/public visitors can browse the catalog.
- Authenticated students can access personalized features like bookmarks and personalized recommendations.
- Admin users can access the Filament dashboard.

File visibility can be public or restricted depending on the file configuration and user access.

## Recommendation and Search Logic Notes
The recommendation system is intentionally hybrid and explainable. The UI is designed to show reasons such as:
- Topik serupa
- Metode sama
- Jenis dokumen sama
- Berdasarkan topik favoritmu
- Populer di repository

This makes the system feel more “intelligent” without requiring a heavy AI stack.

## Current Project State
Recent work in this repository has focused on:
- making the UI feel more polished and coherent
- surfacing recommendation explanations in the UI
- improving the admin dashboard with clearer insights
- organizing admin resources more neatly

## How to Run the Project
From the project root:

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

For background recommendation processing:

```bash
php artisan queue:listen --tries=1 --timeout=0
```

For tests:

```bash
php artisan test --filter ExampleTest
```

## Suggested Prompt for Future AI Use
When asking another AI to work on this project, you can say:

"This is a Laravel-based academic repository system with Filament admin, personalized recommendations, bookmarks, student auth, and publication file access. Please preserve the current architecture and focus on user experience, recommendation quality, and admin dashboard improvements."

## Reusable Terminal Command for Future Context Generation
If you want to generate a compact code bundle for another AI tool later, use:

```bash
npx repomix --output repo-context.xml --include "app/**/*.php,bootstrap/**/*.php,config/**/*.php,database/**/*.php,routes/**/*.php,resources/views/**/*.blade.php,tests/**/*.php,composer.json,package.json,README.md,phpunit.xml,vite.config.js"
```

If you want a more curated bundle, include the project context file itself as additional context:

```bash
npx repomix --output repo-context.xml --include "app/**/*.php,bootstrap/**/*.php,config/**/*.php,database/**/*.php,routes/**/*.php,resources/views/**/*.blade.php,tests/**/*.php,composer.json,package.json,README.md,phpunit.xml,vite.config.js,PROJECT_CONTEXT.md"
```
