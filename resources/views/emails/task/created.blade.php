@component('mail::message')
<h3>Hi, {{ $user->name }}</h3>

<p>A task has been assigned to you</p>

@component('mail::button', ['url' => route('task.all')])
    View Task
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent