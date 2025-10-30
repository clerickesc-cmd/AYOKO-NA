document.getElementById('passwordForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const res = await fetch('update_password.php', { method: 'POST', body: new FormData(e.target) });
    const data = await res.json();
    const msg = document.getElementById('passwordMessage');
    msg.innerHTML = data.message;
    msg.className = 'message ' + (data.success ? 'success' : 'error');

    if (data.success) {
        e.target.reset();
        showPopup();
    }
});
