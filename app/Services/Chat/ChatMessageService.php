<?php

namespace App\Services\Chat;

use App\User;
use App\Models\ChatMessage;
use Illuminate\Support\Collection;
use App\Criteria\Chat\ChatMessagesCriteria;
use App\Services\Requests\Chat\MessageRequest;
use App\Repositories\Contracts\ChatMessageRepository;
use App\Services\Contracts\Chat\ChatMessageService as ChatMessageServiceContract;

class ChatMessageService implements ChatMessageServiceContract
{
    /**
     * @var ChatMessageRepository
     */
    private $chatMessageRepository;

    public function __construct(ChatMessageRepository $chatMessageRepository)
    {
        $this->chatMessageRepository = $chatMessageRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getUserMessages(User $recipient, User $sender): Collection
    {
        return $this->chatMessageRepository->getByCriteria(new ChatMessagesCriteria($recipient, $sender));
    }

    /**
     * {@inheritdoc}
     */
    public function addMessage(MessageRequest $messageRequest, User $sender, User $recipient): ChatMessage
    {
        $chatMessage = new ChatMessage();

        $chatMessage->message = $messageRequest->getMessage();
        $chatMessage->sender_id = $sender->id;
        $chatMessage->recipient_id = $recipient->id;

        return $this->chatMessageRepository->save($chatMessage);
    }

    /**
     * Delete user message
     *
     * @param ChatMessage $message
     * @return int
     */
    public function deleteUserMessage(ChatMessage $message)
    {
        return $this->chatMessageRepository->deleteUserMessage($message);
    }

    /**
     * Mark message as read
     *
     * @param ChatMessage $message
     * @return mixed
     */
    public function markAsRead(ChatMessage $message)
    {
        $attributes = [
            'is_read' => true,
        ];

        return $this->chatMessageRepository->markMessageAsRead(new ChatMessage($attributes), $message->getMessageId());
    }
}
