<div class="list-timeline">
    <h3 class="headerMonth">Month</h3>
    <form method="POST" action="{{ route('update.monthTimeline') }}">
        @csrf
        <button type="submit" name="months" value="3"
            @class([
                'timeline-item',
                'active' => session('timelineSelectedMonth') == 3,
                'inactive' => session('timelineSelectedMonth') != 3,
            ])
        >
            3 Monate
        </button>
        <span></span>
        <button type="submit" name="months" value="6"
            @class([
                'timeline-item',
                'active' => session('timelineSelectedMonth') == 6,
                'inactive' => session('timelineSelectedMonth') != 6,
            ])
        >
            6 Monate
        </button>
        <span></span>
        <button type="submit" name="months" value="12"
            @class([
                'timeline-item',
                'active' => session('timelineSelectedMonth') == 12,
                'inactive' => session('timelineSelectedMonth') != 12,
            ])
        >
            12 Monate
        </button>
        <span></span>
        <button type="submit" name="months" value="24"
            @class([
                'timeline-item',
                'active' => session('timelineSelectedMonth') == 24,
                'inactive' => session('timelineSelectedMonth') != 24,
            ])
        >
            24 Monate
        </button>
    </form>
</div>