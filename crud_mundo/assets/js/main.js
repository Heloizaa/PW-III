function confirmarExclusao(event) {
    if (!confirm("Tem certeza que deseja deletar este registro? Essa ação removerá também dependências em cascata se configurado.")) {
        event.preventDefault();
    }
}

document.addEventListener("DOMContentLoaded", () => {
    const searchInput = document.getElementById("search");
    if (searchInput) {
        searchInput.addEventListener("keyup", function() {
            const value = this.value.toLowerCase();
            const rows = document.querySelectorAll(".dados-tabela tr");
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(value) ? "" : "none";
            });
        });
    }
});