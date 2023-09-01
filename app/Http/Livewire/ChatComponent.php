<?php

namespace App\Http\Livewire;

use App\Models\Chat;
use App\Models\Contact;
use App\Notifications\NewMessage;
use App\Notifications\UserTyping;
use Illuminate\Support\Facades\Notification;
use Livewire\Component;

class ChatComponent extends Component
{

    public $search;
    public $contactChat, $chat;
    public $bodyMessage;
    public $chatId;
    public $users;

    public function mount()
    {
        $this->users = collect();
    }

    //Listener
    public function getListeners()
    {
        $userId = auth()->user()->id;
        return [
            "echo-notification:App.Models.User.{$userId},notification" => 'render',
            "echo-presence:chat.10,here" => 'chatHere',
            "echo-presence:chat.10,joining" => 'chatJoining',
            "echo-presence:chat.10,leaving" => 'chatLeaving'
        ];
    }


    //Computed properties 
    public function getContactsProperty()
    {
        return Contact::where('user_id', auth()->user()->id)
            ->when($this->search, function ($query) {
                $query->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                        ->orWhereHas('user', function ($query) {
                            $query->where('email', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->get() ?? [];
    }

    function getMessagesProperty()
    {
        return $this->chat ? $this->chat->messages()->get() : [];
        /*  return $this->chat ? Message::where('chat_id', $this->chat->id)->get() : []; */
        /* return $this->chat ? $this->chat->messages()->get() : []; */
    }

    public function getChatsProperty()
    {
        return auth()->user()->chats()->get()->sortByDesc('lastMessageAt');
    }
    public function getUsersNotificationsProperty()
    {
        return $this->chat ? $this->chat->users->where('id', '!=', auth()->id()) : collect();
    }
    public function getActiveProperty()
    {
        return $this->users->contains($this->users_notifications->first()->id);
    }


    public function updatedBodyMessage($value)
    {
        if ($value) {
            Notification::send($this->users_notifications, new UserTyping($this->chat->id));
        }
    }


    //Methods

    public function openChatContact(Contact $contact)
    {

        $chat = auth()->user()->chats()
            ->whereHas('users', function ($query) use ($contact) {
                $query->where('user_id', $contact->contact_id);
            })
            ->has('users', 2)

            ->first();
        if ($chat) {
            $this->chat = $chat;
            $this->chatId = $chat->id;
            $this->reset('contactChat', 'bodyMessage', 'search');
        } else {
            $this->contactChat = $contact;
            $this->reset('chat', 'bodyMessage', 'search');
        }
    }
    public function openChat(Chat $chat)
    {
        $this->chat = $chat;
        $this->chatId = $chat->id;
        $this->reset('contactChat', 'bodyMessage');
        
    }

    public function sendMessage()
    {
        $this->validate([
            'bodyMessage' => 'required'
        ]);

        if (!$this->chat) {
            $this->chat = Chat::create();
            $this->chatId = $this->chat->id;
            $this->chat->users()->attach([auth()->user()->id, $this->contactChat->contact_id]);
        }

        $this->chat->messages()->create([
            'body' => $this->bodyMessage,
            'user_id' => auth()->user()->id
        ]);

        Notification::send($this->users_notifications, new NewMessage());
        $this->reset('bodyMessage', 'contactChat');
    }

    public function chatHere($users)
    {
        $this->users = collect($users)->pluck('id');
    }

    public function chatJoining($user)
    {
        $this->users->push($user['id']);
    }
    public function chatLeaving($user)
    {
        $this->users = $this->users->filter(function ($id) use ($user) {
            return $id != $user['id'];
        });
    }

    public function render()
    {
        if ($this->chat) {
            $this->chat->messages()->where('user_id', '!=', auth()->id())->where('is_read', false)->update([
                'is_read' => true
            ]);
            Notification::send($this->users_notifications, new NewMessage());
            $this->emit('scrollIntoView');
        }

        return view('livewire.chat-component')->layout('layouts.chat');
    }
}
