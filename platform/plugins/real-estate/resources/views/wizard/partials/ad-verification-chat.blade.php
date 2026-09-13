@php
    $canReply = in_array($role, ['agent', 'member'], true);
@endphp

<div class="wizard-panel wizard-chat">
    <h2 class="wizard-panel__heading">{{ __('Messages') }}</h2>
    <p class="wizard-panel__description">
        {{ $role === 'admin'
            ? __('Conversation between the agent and the property owner. You can read it, but can\'t reply here.')
            : __('Talk to the agent or property owner about this listing.') }}
    </p>

    <div class="wizard-chat__thread">
        @forelse ($comments as $comment)
            @php
                $senderRole = $comment->member_id ? 'member' : ($comment->admin_id ? 'admin' : 'agent');
                $sender = $senderRole === 'member' ? $comment->member : ($senderRole === 'admin' ? $comment->admin : $comment->user);
                $senderName = $sender ? ($senderRole === 'member' ? $sender->full_name : $sender->getFullName()) : __('Unknown');
                // Agents' real photo is saved to image_path by the admin
                // "Agents" form, not to the avatar_id/MediaFile relation
                // Account::avatar_url reads - check it first, same as the
                // Choose Agent step does, or it always falls back to a
                // generated initials avatar even when a real photo exists.
                $senderAvatar = $sender
                    ? ($senderRole === 'agent' && $sender->image_path ? RvMedia::getImageUrl($sender->image_path) : $sender->avatar_url)
                    : null;
            @endphp
            <div class="wizard-chat__message wizard-chat__message--{{ $senderRole }}">
                @if ($senderAvatar)
                    <img src="{{ $senderAvatar }}" class="wizard-chat__avatar" alt="{{ $senderName }}">
                @endif
                <div class="wizard-chat__bubble">
                    <div class="wizard-chat__meta">
                        <strong>{{ $senderName }}</strong>
                        <span class="wizard-chat__role">{{ __(ucfirst($senderRole)) }}</span>
                        <span class="wizard-chat__time">{{ $comment->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="wizard-chat__text">{{ $comment->comment }}</p>
                </div>
            </div>
        @empty
            <p class="wizard-hint">{{ __('No messages yet.') }}</p>
        @endforelse
    </div>

    @if ($canReply)
        <form method="post" action="{{ $commentStoreUrl }}" class="wizard-chat__form">
            @csrf
            <div class="wizard-field">
                <textarea name="comment" class="wizard-chat__input" rows="3" placeholder="{{ __('Write a message...') }}" required></textarea>
            </div>
            <div class="wizard-panel__actions">
                <span></span>
                <button type="submit" class="wizard-btn wizard-btn--primary">
                    {{ __('Send') }} <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </form>
    @endif
</div>
