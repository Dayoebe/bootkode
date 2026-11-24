<?php

namespace App\Services\Authorization;

use App\Models\Core\User;
use Illuminate\Auth\Access\AuthorizationException;

final class PublishingAccess
{
    public const BLOG_POSTS = 'blog.posts';
    public const BLOG_CATEGORIES = 'blog.categories';
    public const BLOG_COMMENTS = 'blog.comments';
    public const BLOG_SETTINGS = 'blog.settings';
    public const BLOG_SEO = 'blog.seo';
    public const PAGE_CONTENT = 'pages.content';
    public const PAGE_ANALYTICS = 'pages.analytics';
    public const PAGE_SEO = 'pages.seo';
    public const PAGE_SETTINGS = 'pages.settings';
    public const CONTENT_EDITOR = 'content.editor';
    public const CONTENT_ADMINISTRATION = 'content.administration';
    public const NEWSLETTER_EDITOR = 'newsletter.editor';
    public const NEWSLETTER_SETTINGS = 'newsletter.settings';

    private const ROLE_MATRIX = [
        self::BLOG_POSTS => ['super_admin', 'academy_admin', 'content_editor'],
        self::BLOG_CATEGORIES => ['super_admin', 'academy_admin'],
        self::BLOG_COMMENTS => ['super_admin', 'academy_admin', 'content_editor'],
        self::BLOG_SETTINGS => ['super_admin'],
        self::BLOG_SEO => ['super_admin', 'content_editor'],
        self::PAGE_CONTENT => ['super_admin', 'academy_admin', 'content_editor'],
        self::PAGE_ANALYTICS => ['super_admin', 'academy_admin'],
        self::PAGE_SEO => ['super_admin', 'academy_admin'],
        self::PAGE_SETTINGS => ['super_admin'],
        self::CONTENT_EDITOR => ['super_admin', 'academy_admin', 'content_editor'],
        self::CONTENT_ADMINISTRATION => ['super_admin', 'academy_admin'],
        self::NEWSLETTER_EDITOR => ['super_admin', 'academy_admin', 'content_editor'],
        self::NEWSLETTER_SETTINGS => ['super_admin'],
    ];

    public function allows(?User $user, string $ability): bool
    {
        $roles = self::ROLE_MATRIX[$ability] ?? null;

        return $user !== null && $roles !== null && $user->hasAnyRole($roles);
    }

    /** @throws AuthorizationException */
    public function authorize(?User $user, string $ability): void
    {
        if (! $this->allows($user, $ability)) {
            throw new AuthorizationException('You do not have permission to perform this publishing action.');
        }
    }

    /** @return list<string> */
    public function rolesFor(string $ability): array
    {
        return self::ROLE_MATRIX[$ability] ?? [];
    }
}
