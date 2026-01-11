const onLoad = () => {
    const page = document.querySelector('[data-page="error-404"]');
    if (!page) return;

    const darkImage = document.querySelector("#hero-image-dark");
    const lightImage = document.querySelector("#hero-image-light");

    if (!darkImage || !lightImage) return;

    if ($darkmode?.currentMode === "dark") {
        lightImage.classList.add("hidden");
    } else {
        darkImage.classList.add("hidden");
    }
};

window.addEventListener("app:mounted", onLoad, { once: true });
