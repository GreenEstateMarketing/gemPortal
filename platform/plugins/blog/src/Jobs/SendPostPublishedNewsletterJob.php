<?php

namespace Botble\Blog\Jobs;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Blog\Models\Post;
use Botble\Blog\Models\Subscriber;
use EmailHandler;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Log;
use RvMedia;

class SendPostPublishedNewsletterJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var int
     */
    public $postId;

    public function __construct(int $postId)
    {
        $this->postId = $postId;
    }

    public function handle()
    {
        $post = Post::query()->find($this->postId);

        if (!$post || $post->status != BaseStatusEnum::PUBLISHED) {
            return;
        }

        $postUrl = $post->url ?? url('/');
        $postImage = RvMedia::getImageUrl($post->image, 'medium', false, RvMedia::getDefaultImage());
        $postExcerpt = Str::limit(strip_tags($post->description), 160);

        Subscriber::query()
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->cursor()
            ->each(function (Subscriber $subscriber) use ($post, $postUrl, $postImage, $postExcerpt) {
                try {
                    EmailHandler::setModule('blog')
                        ->setVariableValues([
                            'post_title'      => $post->name,
                            'post_excerpt'    => $postExcerpt,
                            'post_url'        => $postUrl,
                            'post_image'      => $postImage,
                            'unsubscribe_url' => route('public.blog.unsubscribe', $subscriber->token),
                        ])
                        ->sendUsingTemplate('post-published', $subscriber->email);
                } catch (Exception $exception) {
                    Log::error('Failed to send newsletter to ' . $subscriber->email . ': ' . $exception->getMessage());
                }
            });
    }
}
