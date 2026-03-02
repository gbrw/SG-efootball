<?php
require_once __DIR__ . '/config.php';

/* ─────────────────────────────────────────────────────────────────────────
   queries.php — MySQL PDO query helpers
   All functions return the same nested structure as before so templates
   remain unchanged: $post['post_translations'][0]['title'], etc.
───────────────────────────────────────────────────────────────────────── */

/**
 * Wrap a flat JOIN row into the nested structure templates expect.
 */
function _wrapPost(array $row): array
{
    $translation = [
        'id'         => $row['t_id']       ?? null,
        'post_id'    => $row['id']          ?? null,
        'language'   => $row['language']    ?? null,
        'title'      => $row['title']       ?? '',
        'slug'       => $row['slug']        ?? '',
        'content'    => $row['content']     ?? '',
        'created_at' => $row['created_at']  ?? null,
        'updated_at' => $row['updated_at']  ?? null,
    ];

    return [
        'id'               => $row['id'],
        'category'         => $row['category'],
        'image_url'        => $row['image_url'],
        'video_url'        => $row['video_url'],
        'created_at'       => $row['created_at'],
        'updated_at'       => $row['updated_at'],
        'post_translations' => [$translation],
    ];
}

/**
 * Fetch paginated posts for a category + locale.
 */
function getPostsByCategory(string $category, string $locale, int $page = 1, int $limit = 12): array
{
    try {
        $offset = ($page - 1) * $limit;
        $sql = "SELECT p.id, p.category, p.image_url, p.video_url,
                       p.created_at, p.updated_at,
                       pt.id AS t_id, pt.language, pt.title, pt.slug, pt.content
                FROM posts p
                INNER JOIN post_translations pt
                       ON pt.post_id = p.id AND pt.language = :lang
                WHERE p.category = :cat
                ORDER BY p.created_at DESC
                LIMIT :lim OFFSET :off";

        $stmt = db()->prepare($sql);
        $stmt->bindValue(':lang', $locale, PDO::PARAM_STR);
        $stmt->bindValue(':cat',  $category, PDO::PARAM_STR);
        $stmt->bindValue(':lim',  $limit,  PDO::PARAM_INT);
        $stmt->bindValue(':off',  $offset, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll();

        return array_map('_wrapPost', $rows);
    } catch (Throwable $e) {
        error_log('getPostsByCategory: ' . $e->getMessage());
        return [];
    }
}

/**
 * Fetch the latest N posts across all categories for a locale.
 */
function getLatestPosts(string $locale, int $limit = 6): array
{
    try {
        $sql = "SELECT p.id, p.category, p.image_url, p.video_url,
                       p.created_at, p.updated_at,
                       pt.id AS t_id, pt.language, pt.title, pt.slug, pt.content
                FROM posts p
                INNER JOIN post_translations pt
                       ON pt.post_id = p.id AND pt.language = :lang
                ORDER BY p.created_at DESC
                LIMIT :lim";

        $stmt = db()->prepare($sql);
        $stmt->bindValue(':lang', $locale, PDO::PARAM_STR);
        $stmt->bindValue(':lim',  $limit,  PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll();

        return array_map('_wrapPost', $rows);
    } catch (Throwable $e) {
        error_log('getLatestPosts: ' . $e->getMessage());
        return [];
    }
}

/**
 * Fetch a single post by slug + locale.
 */
function getPostBySlug(string $slug, string $locale): ?array
{
    try {
        $sql = "SELECT p.id, p.category, p.image_url, p.video_url,
                       p.created_at, p.updated_at,
                       pt.id AS t_id, pt.language, pt.title, pt.slug, pt.content
                FROM posts p
                INNER JOIN post_translations pt
                       ON pt.post_id = p.id AND pt.language = :lang
                WHERE pt.slug = :slug
                LIMIT 1";

        $stmt = db()->prepare($sql);
        $stmt->execute([':lang' => $locale, ':slug' => $slug]);
        $row = $stmt->fetch();

        return $row ? _wrapPost($row) : null;
    } catch (Throwable $e) {
        error_log('getPostBySlug: ' . $e->getMessage());
        return null;
    }
}

/**
 * Get both language slugs of a post (for hreflang alternates).
 * Returns ['ar' => 'slug-ar', 'en' => 'slug-en']
 */
function getPostAlternates(int $postId): array
{
    try {
        $stmt = db()->prepare(
            "SELECT language, slug FROM post_translations WHERE post_id = :id"
        );
        $stmt->execute([':id' => $postId]);
        $result = [];
        foreach ($stmt->fetchAll() as $row) {
            $result[$row['language']] = $row['slug'];
        }
        return $result;
    } catch (Throwable $e) {
        return [];
    }
}

/**
 * Extract the single translation array from a post row.
 */
function getTranslation(array $post): ?array
{
    $t = $post['post_translations'] ?? [];
    return is_array($t) ? ($t[0] ?? null) : null;
}
