<?php

namespace RTippin\MessengerBots\Bots;

use RTippin\Messenger\Actions\Bots\BotActionHandler;
use RTippin\Messenger\Actions\Messages\StoreMessage;
use RTippin\Messenger\Contracts\EmojiInterface;
use Throwable;

class ReplyBot extends BotActionHandler
{
    /**
     * @var StoreMessage
     */
    private StoreMessage $storeMessage;

    /**
     * @var EmojiInterface
     */
    private EmojiInterface $emoji;

    /**
     * ReplyBot constructor.
     *
     * @param StoreMessage $storeMessage
     * @param EmojiInterface $emoji
     */
    public function __construct(StoreMessage $storeMessage, EmojiInterface $emoji)
    {
        $this->storeMessage = $storeMessage;
        $this->emoji = $emoji;
    }

    /**
     * The bots settings.
     *
     * @return array
     */
    public static function getSettings(): array
    {
        return [
            'alias' => 'reply',
            'description' => 'Reply with the given response(s).',
            'name' => 'Reply',
        ];
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'replies' => ['required', 'array', 'min:1', 'max:5'],
            'replies.*' => ['required', 'string'],
            'quote_original' => ['required', 'boolean'],
        ];
    }

    /**
     * @return array
     */
    public function errorMessages(): array
    {
        return [
            'replies.*.required' => 'Reply is required.',
            'replies.*.string' => 'A reply must be a string.',
        ];
    }

    /**
     * @param array|null $payload
     * @return string|null
     */
    public function serializePayload(?array $payload): ?string
    {
        $payload['replies'] = collect($payload['replies'])
            ->transform(fn ($reply) => $this->emoji->toShort($reply))
            ->toArray();

        return json_encode($payload);
    }

    /**
     * @throws Throwable
     */
    public function handle(): void
    {
        $replies = $this->getPayload('replies');

        foreach ($replies as $key => $reply) {
            if ($key === array_key_first($replies) && $this->getPayload('quote_original')) {
                $this->storeMessage->execute($this->thread, [
                    'message' => $reply,
                    'reply_to_id' => $this->message->id,
                ]);

                continue;
            }

            $this->storeMessage->execute($this->thread, [
                'message' => $reply,
            ]);
        }
    }
}
