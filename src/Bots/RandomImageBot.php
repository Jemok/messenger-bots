<?php

namespace RTippin\MessengerBots\Bots;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use RTippin\Messenger\Actions\Messages\StoreImageMessage;
use RTippin\Messenger\Contracts\BotHandler;
use RTippin\Messenger\Exceptions\InvalidProviderException;
use RTippin\Messenger\Messenger;
use RTippin\Messenger\Models\BotAction;
use RTippin\Messenger\Models\Message;
use Throwable;

class RandomImageBot implements BotHandler
{
    /**
     * @var Messenger
     */
    private Messenger $messenger;

    /**
     * @var StoreImageMessage $storeImage
     */
    private StoreImageMessage $storeImage;

    /**
     * RandomImageBot constructor.
     *
     * @param Messenger $messenger
     * @param StoreImageMessage $storeImage
     */
    public function __construct(Messenger $messenger, StoreImageMessage $storeImage)
    {
        $this->messenger = $messenger;
        $this->storeImage = $storeImage;
    }

    /**
     * @param BotAction $action
     * @param Message $message
     * @param string $matchingTrigger
     * @throws InvalidProviderException
     */
    public function execute(BotAction $action, Message $message, string $matchingTrigger): void
    {
        $name = uniqid();
        $file = '/tmp/'.$name;
        file_put_contents($file, Http::timeout(30)->get(config('messenger-bots.random_image_url'))->body());

        $this->messenger->setProvider($action->bot);

        try {
            $this->storeImage->execute($message->thread, [
                'image' => new UploadedFile($file, $name),
            ]);
        } catch (Throwable $e) {
            report($e);
        }

        unlink($file);
    }
}
