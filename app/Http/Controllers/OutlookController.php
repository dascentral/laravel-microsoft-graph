<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\TokenStore\TokenCache;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;

class OutlookController extends Controller
{
    /**
     * @var \Microsoft\Graph\Graph
     */
    protected $graph;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Graph $graph)
    {
        $this->graph = $graph;
    }

    /**
     * Show the mail.
     *
     * @return \Illuminate\Http\Response
     */
    public function mail()
    {
        $this->setAccessToken();

        // query current user
        $user = $this->graph->createRequest('GET', '/me')->setReturnType(Model\User::class)->execute();
        $username = $user->getDisplayName();

        // query emails
        $messageQueryParams = [
            '$select' => 'subject,receivedDateTime,from',
            '$orderby' => 'receivedDateTime DESC',
            '$top' => '10',
        ];
        $getMessagesUrl = '/me/mailfolders/inbox/messages?' . http_build_query($messageQueryParams);
        $messages = collect($this->graph->createRequest('GET', $getMessagesUrl)->setReturnType(Model\Message::class)->execute());

        // return a view
        return view('mail', compact('username', 'messages'));
    }

    /**
     * Show the calendar events.
     *
     * @return \Illuminate\Http\Response
     */
    public function calendar()
    {
        $this->setAccessToken();

        // query current user
        $user = $this->graph->createRequest('GET', '/me')->setReturnType(Model\User::class)->execute();

        // query calendar events
        $eventsQueryParams = [
            '$select' => 'subject,start,end',
            '$orderby' => 'Start/DateTime DESC',
            '$top' => '10',
        ];
        $getEventsUrl = '/me/events?' . http_build_query($eventsQueryParams);
        $events = collect($this->graph->createRequest('GET', $getEventsUrl)->setReturnType(Model\Event::class)->execute());

        // return a view
        return view('calendar', [
            'username' => $user->getDisplayName(),
            'events' => $events,
        ]);
    }

    /**
     * Show the contacts.
     *
     * @return \Illuminate\Http\Response
     */
    public function contacts()
    {
        $this->setAccessToken();

        // query current user
        $user = $this->graph->createRequest('GET', '/me')->setReturnType(Model\User::class)->execute();

        // query contacts
        $contactsQueryParams = [
            '$select' => 'givenName,surname,emailAddresses',
            '$orderby' => 'givenName ASC',
            '$top' => '10',
        ];
        $getContactsUrl = '/me/contacts?' . http_build_query($contactsQueryParams);
        $contacts = collect($this->graph->createRequest('GET', $getContactsUrl)->setReturnType(Model\Contact::class)->execute());

        // return a view
        return view('contacts', [
            'username' => $user->getDisplayName(),
            'contacts' => $contacts,
        ]);
    }

    /**
     * Set the access token.
     *
     * @return \Illuminate\Http\Response
     */
    protected function setAccessToken()
    {
        $token = (new TokenCache)->getAccessToken();
        $this->graph->setAccessToken($token);
    }
}
