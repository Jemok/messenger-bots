<?php

namespace RTippin\MessengerBots\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use RTippin\Messenger\Models\Thread;

class BotPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the provider can view thread bots.
     *
     * @param $user
     * @param Thread $thread
     * @return Response
     */
    public function viewAny($user, Thread $thread): Response
    {
        return $thread->hasCurrentProvider()
            ? $this->allow()
            : $this->deny('Not authorized to view bots.');
    }

    /**
     * Determine whether the provider can view the bot.
     *
     * @param $user
     * @param Thread $thread
     * @return Response
     */
    public function view($user, Thread $thread): Response
    {
        return $thread->hasCurrentProvider()
            ? $this->allow()
            : $this->deny('Not authorized to view bot.');
    }

    /**
     * Determine whether the provider can create a new bot.
     *
     * @param $user
     * @param Thread $thread
     * @return Response
     */
    public function create($user, Thread $thread): Response
    {
        return $thread->isAdmin()
        && ! $thread->isLocked()
            ? $this->allow()
            : $this->deny('Not authorized to create a bot.');
    }

    /**
     * Determine whether the provider can edit the bot.
     *
     * @param $user
     * @param Thread $thread
     * @return Response
     */
    public function update($user, Thread $thread): Response
    {
        return $thread->isAdmin()
        && ! $thread->isLocked()
            ? $this->allow()
            : $this->deny('Not authorized to update bot.');
    }

    /**
     * Determine whether the provider can delete the bot.
     *
     * @param $user
     * @param Thread $thread
     * @return Response
     */
    public function delete($user, Thread $thread): Response
    {
        return $thread->isAdmin()
        && ! $thread->isLocked()
            ? $this->allow()
            : $this->deny('Not authorized to remove bot.');
    }
}
