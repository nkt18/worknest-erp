<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Auto hide alerts after 4 seconds
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(el => {
            el.style.transition = "opacity 0.5s";
            el.style.opacity = 0;
        });
    }, 4000);
</script>

</body>
</html>