<?php

namespace Botble\Blog\Http\Controllers;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Blog\Models\Category;
use Botble\Blog\Models\Post;
use Botble\Blog\Models\Subscriber;
use Botble\Blog\Models\Tag;
use Botble\Blog\Repositories\Interfaces\PostInterface;
use Botble\Blog\Services\BlogService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Response;
use SeoHelper;
use SlugHelper;
use Theme;

class PublicController extends Controller
{
    /**
     * @param Request $request
     * @param PostInterface $postRepository
     * @return Response
     */
    public function getIndex(Request $request, PostInterface $postRepository)
    {
        SeoHelper::setTitle(__('Blog'))
            ->setDescription(__('Blog'));

        Theme::breadcrumb()
            ->add(__('Home'), url('/'))
            ->add(__('Blog'), route('public.blog'));

        $categoryId = $request->input('category_id');
        $keyword = $request->input('q');
        $hasFilters = (bool) ($categoryId || $keyword);

        $featuredPost = get_featured_posts(1, ['slugable', 'categories', 'author'])->first();

        if (!$featuredPost) {
            $featuredPost = Post::query()
                ->with(['slugable', 'categories', 'author'])
                ->where('status', BaseStatusEnum::PUBLISHED)
                ->orderByDesc('created_at')
                ->first();
        }

        $posts = Post::query()
            ->with(['slugable', 'categories'])
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->when($featuredPost && !$hasFilters, fn ($query) => $query->where('id', '!=', $featuredPost->id))
            ->when($categoryId, fn ($query) => $query->whereHas('categories', fn ($q) => $q->where('categories.id', $categoryId)))
            ->when($keyword, fn ($query) => $query->where('name', 'LIKE', '%' . $keyword . '%'))
            ->orderByDesc('created_at')
            ->paginate((int) ($request->input('per_page') ?: 6));

        $categories = Category::query()
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->orderBy('order')
            ->get();

        return Theme::scope(
            'blog.blog',
            compact('posts', 'featuredPost', 'categories', 'categoryId', 'keyword', 'hasFilters'),
            'plugins/blog::themes.loop'
        )->render();
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function postSubscribe(Request $request, BaseHttpResponse $response)
    {
        $request->validate(['email' => 'required|email|max:191']);

        $subscriber = Subscriber::query()->firstOrNew(['email' => $request->input('email')]);
        $subscriber->status = BaseStatusEnum::PUBLISHED;
        $subscriber->save();

        // Dedicated flash flag (rather than relying on the shared success_msg
        // alone) so the footer/newsletter partials - visible on every public
        // page - only show this specific message, not any other unrelated
        // BaseHttpResponse-based form's flash message sitewide.
        session()->flash('newsletter_subscribed', true);

        return $response->setMessage(__('Thank you for subscribing to our newsletter!'));
    }

    /**
     * @param string $token
     * @return \Illuminate\Http\RedirectResponse
     */
    public function getUnsubscribe($token)
    {
        $subscriber = Subscriber::query()->where('token', $token)->first();

        if ($subscriber) {
            // BaseStatusEnum has no "unsubscribed" value of its own - DRAFT
            // (not live/not published) is reused here as "inactive" rather
            // than inventing a new enum just for this one flag.
            $subscriber->status = BaseStatusEnum::DRAFT;
            $subscriber->save();
        }

        return redirect()
            ->route('public.blog')
            ->with('newsletter_unsubscribed', true)
            ->with('success_msg', __("You've been unsubscribed from our newsletter."));
    }

    /**
     * @param Request $request
     * @param PostInterface $postRepository
     * @return Response
     */
    public function getSearch(Request $request, PostInterface $postRepository)
    {
        $query = $request->input('q');
        SeoHelper::setTitle(__('Search result for: ') . '"' . $query . '"')
            ->setDescription(__('Search result for: ') . '"' . $query . '"');

        $posts = $postRepository->getSearch($query, 0, 12);

        Theme::breadcrumb()
            ->add(__('Home'), url('/'))
            ->add(__('Search result for: ') . '"' . $query . '"', route('public.search'));

        return Theme::scope('search', compact('posts'))
            ->render();
    }

    /**
     * @param string $slug
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|Response
     */
    public function getTag($slug, BlogService $blogService)
    {
        $slug = SlugHelper::getSlug($slug, SlugHelper::getPrefix(Tag::class));

        if (!$slug) {
            abort(404);
        }

        $data = $blogService->handleFrontRoutes($slug);

        if (isset($data['slug']) && $data['slug'] !== $slug->key) {
            return redirect()->to(url(SlugHelper::getPrefix(Tag::class) . '/' . $data['slug']));
        }

        return Theme::scope($data['view'], $data['data'], $data['default_view'])
            ->render();
    }

    /**
     * @param string $slug
     * @param BlogService $blogService
     * @return \Illuminate\Http\RedirectResponse|Response
     */
    public function getPost($slug, BlogService $blogService)
    {
        $slug = SlugHelper::getSlug($slug, SlugHelper::getPrefix(Post::class, 'blog'));

        if (!$slug) {
            abort(404);
        }

        $data = $blogService->handleFrontRoutes($slug);

        if (isset($data['slug']) && $data['slug'] !== $slug->key) {
            return redirect()->to(url(SlugHelper::getPrefix(Post::class, 'blog') . '/' . $data['slug']));
        }

        return Theme::scope($data['view'], $data['data'], $data['default_view'])
            ->render();
    }

    /**
     * @param string $slug
     * @param BlogService $blogService
     * @return \Illuminate\Http\RedirectResponse|Response
     */
    public function getCategory($slug, BlogService $blogService)
    {
        $slug = SlugHelper::getSlug($slug, SlugHelper::getPrefix(Category::class));

        if (!$slug) {
            abort(404);
        }

        $data = $blogService->handleFrontRoutes($slug);

        if (isset($data['slug']) && $data['slug'] !== $slug->key) {
            return redirect()->to(url(SlugHelper::getPrefix(Category::class) . '/' . $data['slug']));
        }

        return Theme::scope($data['view'], $data['data'], $data['default_view'])
            ->render();
    }
}
