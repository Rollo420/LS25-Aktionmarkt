<div class="list-timeline">
    <h3 class="headerMonth">Month</h3>
    <form method="POST" action="{{ route('update.monthTimeline') }}">
        @csrf
        <button type="submit" name="months" value="3" class="timeline-item {{ session('timelineSelectedMonth') == 3 ? 'active' : 'inactive' }}">
            3 Monate
        </button>
        <span></span>
        <button type="submit" name="months" value="6" class="timeline-item {{ session('timelineSelectedMonth') == 6 ? 'active' : 'inactive' }}">
            6 Monate
        </button>
        <span></span>
        <button type="submit" name="months" value="12" class="timeline-item {{ session('timelineSelectedMonth') == 12 ? 'active' : 'inactive' }}">
            12 Monate
        </button>        
        <span></span>
        <button type="submit" name="months" value="24" class="timeline-item {{ session('timelineSelectedMonth') == 24 ? 'active' : 'inactive' }}">
            24 Monate
        </button>       

    </form>
</div>