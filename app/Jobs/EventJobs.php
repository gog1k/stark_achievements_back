<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\EventPartnerUser;
use Exception;

class EventJobs extends Jobs
{

    /**
     * @var string
     */
    public $queue = 'events';

    /**
     * @var EventPartnerUser
     */
    private EventPartnerUser $eventPartnerUser;

    /**
     * @var string
     */
    private string $eventType;

    /**
     * Create a new job instance.
     *
     * @param string $eventType
     * @param EventPartnerUser $eventPartnerUser
     */
    public function __construct(string $eventType, EventPartnerUser $eventPartnerUser)
    {
        $this->eventType = $eventType;
        $this->eventPartnerUser = $eventPartnerUser;
    }

    /**
     * Execute the job.
     *
     * @return bool
     * @throws Exception
     */
    public function handle(): bool
    {
        $eventPartnerUser = $this->eventPartnerUser->refresh();
        $achievment = $eventPartnerUser->event->achievements()->where([
            'event_fields_hash' => $eventPartnerUser->fields_hash,
        ])->first();

        if (
            $eventPartnerUser
            && $achievment
            && empty($achievment->partnerUsers()->where(['user_id' => $eventPartnerUser->user_id])->first())
            && $eventPartnerUser->count >= $achievment->count
        ) {
            $achievment->partnerUsers()->sync($eventPartnerUser->id, false);
        }

        return true;
    }
}
