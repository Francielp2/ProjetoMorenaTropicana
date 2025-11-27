        </main>
    </div>

    <div id="adminModal" class="admin-modal">
        <div class="admin-modal-content">
            <div class="admin-modal-header">
                <h2 class="admin-modal-title" id="modalTitle">Título do Modal</h2>
                <button class="admin-modal-close" onclick="fecharModal()">
                    <i class="ri-close-line"></i>
                </button>
            </div>
            <div id="modalBody"></div>
        </div>
    </div>

    <script>
        function abrirModal(titulo, conteudo) {
            document.getElementById('modalTitle').textContent = titulo;
            document.getElementById('modalBody').innerHTML = conteudo;
            document.getElementById('adminModal').classList.add('active');
        }

        function fecharModal() {
            document.getElementById('adminModal').classList.remove('active');
        }

        document.getElementById('adminModal').addEventListener('click', function(e) {
            if (e.target === this) {
                fecharModal();
            }
        });
    </script>
</body>
</html>

