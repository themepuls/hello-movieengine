# Theme layout templates (`inc/templates/`)

PHP views for **site chrome** (header, footer, mobile nav) and **Movie Engine overrides** — not blog loops.

## Layout (by area → style)

```
inc/templates/header/style-one/transparent.php
inc/templates/header/style-two/transparent.php
inc/templates/footer/style-one/default.php
inc/templates/navigation/style-one/bottom-nav.php
```

Loaded via `hello_movieengine_get_style_template_part( 'header', 'transparent' )`.

## Movie Engine plugin overrides

Mirror plugin paths under the active style:

```
inc/templates/movie-engine/style-two/loop/content-movie.php
```

Resolved by `movie_engine_get_template_part` filter in `Hello_Movie_Engine_Style_Registry`.

## `template-parts/` (WordPress convention)

Keep only classic template fragments used by `get_template_part()`:

- `content.php`, `content-page.php`, `content-search.php`, `content-none.php`
- `page-title.php`