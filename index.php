<?php
$conn = new mysqli("localhost", "admin", "Password123!", "shop_db");

// Handle Create/Update
if (isset($_POST['action'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $priority = $_POST['priority'];
    $date = $_POST['due_date'];
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $id = $_POST['task_id'];

    if ($_POST['action'] == 'save') {
        if ($id) {
            $conn->query("UPDATE tasks SET title='$title', priority='$priority', due_date='$date', description='$desc' WHERE id=$id");
        } else {
            $conn->query("INSERT INTO tasks (title, priority, due_date, description) VALUES ('$title', '$priority', '$date', '$desc')");
        }
    }
    header("Location: tasks.php");
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM tasks WHERE id=$id");
    header("Location: tasks.php");
}

$tasks = $conn->query("SELECT * FROM tasks ORDER BY due_date ASC");
?>

<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <title>TaskForce | DevOps Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.css" rel="stylesheet" />
    <style>
        body { background-color: #0f172a; color: #f8fafc; }
        .glass { background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.1); }
    </style>
</head>
<body class="font-sans antialiased">

<div class="flex">
    <aside class="w-64 h-screen sticky top-0 bg-slate-900 border-r border-slate-800 p-6">
        <div class="flex items-center gap-3 mb-10">
            <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-500/50">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <span class="text-xl font-black tracking-widest">TASKFORCE</span>
        </div>
        <nav class="space-y-4">
            <a href="#" class="flex items-center gap-3 p-3 bg-indigo-600/10 text-indigo-400 rounded-xl border border-indigo-500/20">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h7" stroke-width="2"/></svg> Dashboard
            </a>
        </nav>
    </aside>

    <main class="flex-1 p-10">
        <div class="flex justify-between items-end mb-10">
            <div>
                <h1 class="text-4xl font-extrabold mb-2">Project Tasks</h1>
                <p class="text-slate-400">Manage development sprints and critical hotfixes.</p>
            </div>
            <button onclick="openForm()" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-500 rounded-xl font-bold transition-all flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="2"/></svg> Create Task
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            <?php while($t = $tasks->fetch_assoc()): ?>
            <div class="glass p-6 rounded-3xl hover:border-indigo-500/50 transition-all group relative">
                <div class="flex justify-between items-start mb-4">
                    <span class="px-3 py-1 rounded-lg text-xs font-black uppercase tracking-tighter 
                        <?php echo $t['priority'] == 'High' ? 'bg-red-500/20 text-red-400' : ($t['priority'] == 'Medium' ? 'bg-amber-500/20 text-amber-400' : 'bg-emerald-500/20 text-emerald-400'); ?>">
                        <?php echo $t['priority']; ?> Priority
                    </span>
                    <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                        <button onclick='editTask(<?php echo json_encode($t); ?>)' class="text-slate-400 hover:text-white"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" stroke-width="2"/></svg></button>
                        <a href="?delete=<?php echo $t['id']; ?>" class="text-slate-400 hover:text-red-500"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7" stroke-width="2"/></svg></a>
                    </div>
                </div>
                <h3 class="text-xl font-bold mb-2"><?php echo htmlspecialchars($t['title']); ?></h3>
                <p class="text-slate-400 text-sm mb-6 line-clamp-2"><?php echo htmlspecialchars($t['description']); ?></p>
                <div class="pt-4 border-t border-white/5 flex justify-between items-center">
                    <div class="flex items-center gap-2 text-slate-500 text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2"/></svg>
                        <?php echo date('M d, Y', strtotime($t['due_date'])); ?>
                    </div>
                    <button onclick='viewTask(<?php echo json_encode($t); ?>)' class="text-indigo-400 text-xs font-bold hover:underline">Details →</button>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </main>
</div>

<div id="task-modal" tabindex="-1" class="hidden fixed inset-0 z-50 flex justify-center items-center bg-black/60 backdrop-blur-sm">
    <div class="glass w-full max-w-lg p-8 rounded-3xl">
        <h2 id="modal-title" class="text-2xl font-black mb-6">Create New Task</h2>
        <form action="" method="POST">
            <input type="hidden" name="task_id" id="task_id">
            <input type="hidden" name="action" value="save">
            <div class="space-y-5">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-2">Task Title</label>
                    <input type="text" name="title" id="title" required class="w-full bg-slate-800 border-none rounded-xl p-4 focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-500 mb-2">Priority</label>
                        <select name="priority" id="priority" class="w-full bg-slate-800 border-none rounded-xl p-4 focus:ring-2 focus:ring-indigo-500">
                            <option value="Low">Low</option>
                            <option value="Medium">Medium</option>
                            <option value="High">High</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-500 mb-2">Due Date</label>
                        <input type="date" name="due_date" id="due_date" required class="w-full bg-slate-800 border-none rounded-xl p-4 focus:ring-2 focus:ring-indigo-500 text-white">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-2">Description</label>
                    <textarea name="description" id="description" rows="3" class="w-full bg-slate-800 border-none rounded-xl p-4 focus:ring-2 focus:ring-indigo-500"></textarea>
                </div>
            </div>
            <div class="mt-8 flex gap-3">
                <button type="submit" class="flex-1 py-4 bg-indigo-600 rounded-xl font-bold hover:bg-indigo-500 transition-all">Deploy Task</button>
                <button type="button" onclick="closeModal()" class="px-8 py-4 bg-slate-800 rounded-xl font-bold hover:bg-slate-700">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.js"></script>
<script>
    const modal = document.getElementById('task-modal');
    function openForm() {
        document.getElementById('task_id').value = "";
        document.getElementById('modal-title').innerText = "Create New Task";
        modal.classList.remove('hidden');
    }
    function closeModal() { modal.classList.add('hidden'); }
    function editTask(t) {
        document.getElementById('task_id').value = t.id;
        document.getElementById('title').value = t.title;
        document.getElementById('priority').value = t.priority;
        document.getElementById('due_date').value = t.due_date;
        document.getElementById('description').value = t.description;
        document.getElementById('modal-title').innerText = "Update Task";
        modal.classList.remove('hidden');
    }
    function viewTask(t) {
        alert("Task: " + t.title + "\n\nDescription: " + t.description);
    }
</script>
</body>
</html>