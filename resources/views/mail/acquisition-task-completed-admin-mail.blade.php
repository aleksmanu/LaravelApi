@component('mail::message')
# Hi {{$user->first_name}},

The following acquisitions task has been marked as completed in the Cluttons Portal:

@component('mail::panel')
    <ul>
        <li><strong>Acquisition: </strong>value</li>
        <li><strong>Site: </strong>value</li>
        <li><strong>Task: </strong>value</li>
        <li><strong>Completed by : </strong>value</li>
        <li><strong>Original target date: </strong>value</li>
        <li><strong>Forecast date: </strong>value</li>
        <li><strong>Completion date: </strong>value</li>
    </ul>
@endcomponent

The next task in this workflow is:

@component('mail::panel')
    <ul>
        <li><strong>Task: </strong>value</li>
        <li><strong>Original target date: </strong>value</li>
        <li><strong>Current forecast date: </strong>value</li>
    </ul>
@endcomponent

To see more details on the task just completed, or any other part of the acquisition process, please log in to the Cluttons Portal

@component('mail::button', ['url' => 'http://localhost:4200'])
    Log In
@endcomponent

Kind regards,<br>
{{ config('app.name') }} Team
@endcomponent
