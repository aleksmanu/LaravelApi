@component('mail::message')
    # Hi {{$user->first_name}},

    The task [name of task just completed] has been completed by [user forename + user surname], which now means the following has been assigned to you for completion:

    @component('mail::panel')
        <ul>
            <li><strong>Acquisition: </strong>value</li>
            <li><strong>Site: </strong>value</li>
            <li><strong>Task: </strong>value</li>
            <li><strong>Original target date: </strong>value</li>
            <li><strong>Original forecast date: </strong>value</li>
        </ul>
    @endcomponent

    To see more details on the task just completed, or any other part of the acquisition process, please log in to the Cluttons Portal

    @component('mail::button', ['url' => 'http://localhost:4200'])
        Log In
    @endcomponent

    Kind regards,<br>
    {{ config('app.name') }} Team
@endcomponent
