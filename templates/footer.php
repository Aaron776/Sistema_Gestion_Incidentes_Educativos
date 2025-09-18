</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Toggle sidebar on mobile
    document.getElementById('menuToggle').addEventListener('click', function() {
        document.querySelector('.sidebar').classList.toggle('open');
        document.querySelector('.overlay').classList.toggle('open');
    });
    
    // Close sidebar when clicking overlay
    document.querySelector('.overlay').addEventListener('click', function() {
        document.querySelector('.sidebar').classList.remove('open');
        document.querySelector('.overlay').classList.remove('open');
    });
    
    // Initialize chart
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('incidentChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Hardware', 'Software', 'Red', 'Seguridad', 'Otros'],
                datasets: [{
                    label: 'Incidentes por Categoría',
                    data: [12, 19, 8, 5, 3],
                    backgroundColor: [
                        'rgba(58, 12, 163, 0.8)',
                        'rgba(67, 97, 238, 0.8)',
                        'rgba(114, 9, 183, 0.8)',
                        'rgba(247, 37, 133, 0.8)',
                        'rgba(76, 201, 240, 0.8)'
                    ],
                    borderColor: [
                        'rgba(58, 12, 163, 1)',
                        'rgba(67, 97, 238, 1)',
                        'rgba(114, 9, 183, 1)',
                        'rgba(247, 37, 133, 1)',
                        'rgba(76, 201, 240, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    });
</script>
</body>
</html>