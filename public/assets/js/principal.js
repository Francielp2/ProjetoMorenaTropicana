// Inicia o NiceSelect para a barra de pesquisa

NiceSelect.bind(document.getElementById("select_navbar"), {
    searchable: false, 
});

// botão de voltar para o inicio

const scrollup = () => {
    const btn = document.getElementById("scrollup");

    window.scrollY >= 350
        ? btn.classList.add("show-scroll")
        : btn.classList.remove("show-scroll");
}

window.addEventListener("scroll", scrollup);

// js da página de detalhes dos produtos




