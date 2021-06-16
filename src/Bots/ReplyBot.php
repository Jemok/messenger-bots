<?php

namespace RTippin\MessengerBots\Bots;

use RTippin\Messenger\Actions\Bots\BotActionHandler;
use RTippin\Messenger\Actions\Messages\StoreMessage;
use Throwable;

class ReplyBot extends BotActionHandler
{
    /**
     * @var StoreMessage
     */
    private StoreMessage $storeMessage;

    /**
     * ReplyBot constructor.
     *
     * @param StoreMessage $storeMessage
     */
    public function __construct(StoreMessage $storeMessage)
    {
        $this->storeMessage = $storeMessage;
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
            'description' => 'Reply with the defined response(s).',
            'name' => 'Reply Bot',
            'unique' => false,
        ];
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'replies' => ['required', 'array', 'min:1'],
            'replies.*' => ['required', 'string'],
            'quote_original' => ['required', 'boolean'],
        ];
    }

    /**
     * @throws Throwable
     */
    public function handle(): void
    {
        $replies = $this->getPayload('replies');

        foreach ($replies as $key => $reply) {
            if ($key === array_key_first($replies) && $this->getPayload('quote_original')) {
                $this->storeMessage->execute($this->message->thread, [
                    'message' => $reply,
                    'reply_to_id' => $this->message->id,
                ]);

                continue;
            }

            $this->storeMessage->execute($this->message->thread, [
                'message' => $reply,
            ]);
        }
    }
}
