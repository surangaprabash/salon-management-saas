<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <h1 class="h3">Welcome, {{ auth()->user()->name }}</h1>
    <p class="text-muted">Phase 1 placeholder. The real dashboard arrives in Phase 3.</p>

    <form method="POST" action="{{ route('admin.logout') }}">
        @csrf
        <button class="btn btn-outline-secondary">Logout</button>
    </form>
</div>
</body>
</html>