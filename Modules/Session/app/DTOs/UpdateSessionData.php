<?php

namespace Modules\Session\DTOs;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class UpdateSessionData extends Data
{
    /**
     * Create a DTO containing optional session update fields.
     *
     * Each parameter represents an updatable session attribute. Parameters typed with `Optional`
     * may be omitted when performing a partial update; parameters that are also `null` allow
     * explicitly clearing the corresponding field.
     *
     * @param string|Optional $scheduled_at New scheduled start time for the session (ISO 8601 string) or omitted.
     * @param int|Optional $duration_minutes New session duration in minutes or omitted.
     * @param string|Optional $type New session type identifier or omitted.
     * @param string|null|Optional $notes New session notes, `null` to clear, or omitted.
     * @param string|Optional $status New session status or omitted.
     * @param string|null|Optional $cancellation_reason New cancellation reason, `null` to clear, or omitted.
     */
    public function __construct(
        public string|Optional $scheduled_at,
        public int|Optional $duration_minutes,
        public string|Optional $type,
        public string|null|Optional $notes,
        public string|Optional $status,
        public string|null|Optional $cancellation_reason,
    ) {}
}
