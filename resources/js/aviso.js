// resources/js/alerta.js
window.Alerta = {
    show: function(mensaje, tipo = 'info') {
        const contenedor = document.getElementById('js-alerta-global');
        const texto = document.getElementById('js-mensaje-global');
        
        if (!contenedor || !texto) return;

        // Lógica de clases (igual que la anterior)...
        contenedor.className = "border px-4 py-3 rounded relative mb-4"; // Reset
        
        const colores = {
            success: 'bg-green-100 border-green-400 text-green-700',
            error: 'bg-red-100 border-red-400 text-red-700',
            warning: 'bg-orange-100 border-orange-400 text-orange-700'
        };

        contenedor.classList.add(...(colores[tipo] || colores.warning).split(' '));
        texto.innerText = mensaje;
        contenedor.classList.remove('hidden');

        setTimeout(() => contenedor.classList.add('hidden'), 5000);
    }
};