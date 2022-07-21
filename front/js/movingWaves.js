/* globals window */
(function () {
    let WATER_TOP_COLOR = "#0a4fa5";
    let WATER_BOTTOM_COLOR = "#3B87F1";

    let htmlCanvas = document.querySelector("canvas");
    let ctx = htmlCanvas.getContext('2d');
    let container = document.querySelector(".waves-container");

    let screenWidth;
    let screenHeight;

    let wave = {};
    let waveLength = 0;
    let wave2 = {};
    let wave3 = {};
    let waves = {};

    let moveWavesId;

    function value(x, width, numberOfWaves) {
        x = x * numberOfWaves / width * 2 * Math.PI;
        return Math.sin(x);
    }

    function multiplier(x, width) {

        let multiplierVar = 120

        if(window.innerWidth < 810) {
            multiplierVar = 50;
        }

        if (x <= width / 2) {
            return x * multiplierVar * 2 / width;
        }
        return width * multiplierVar / 2 / x;
    }

    function randomIntFromInterval(min, max) {
        return Math.floor(Math.random() * (max - min + 1) + min);
    }

    function draw() {
        ctx.clearRect(0, 0, screenWidth, 230);
        ctx.beginPath();
        ctx.moveTo(screenWidth, screenHeight);

        for (let x = waveLength - 1; x > 0; x--) {
            ctx.lineTo(x, waves[x])
        }

        let gradient = ctx.createLinearGradient(0, 0, 0, screenHeight);
        gradient.addColorStop(.5, WATER_TOP_COLOR);
        gradient.addColorStop(1, WATER_BOTTOM_COLOR);
        ctx.fillStyle = gradient;
        ctx.lineTo(0, screenHeight);
        ctx.fill();

        requestAnimationFrame(draw);
    }

    function initializeWaves() {
        let randomWaves1 = randomIntFromInterval(4, 5);
        let randomWaves2 = randomIntFromInterval(2, 3);
        let randomWaves3 = randomIntFromInterval(6, 7);

        for (let x = 0; x < screenWidth; x++) {
            wave[x] = value(x, screenWidth, randomWaves1) * multiplier(x, screenWidth) / 4;
            wave2[x] = value(x, screenWidth, randomWaves2) * multiplier(x * 3, screenWidth) / 6;
            wave3[x] = value(x, screenWidth, randomWaves3);
        }
        waveLength = Object.keys(wave).length;
    }

    function moveWaves() {
        if (!waveLength) {
            initializeWaves();
        }

        for (let x = waveLength - 1; x >= 0; x--) {
            if (x === 0) {
                wave2[x] = wave2[waveLength - 1];
                wave3[x] = wave3[waveLength - 1];
            } else {
                wave2[x] = wave2[x - 1];
                wave3[x] = wave3[x - 1];
            }

            waves[x] = wave[x] + wave2[x] + wave3[x] + screenHeight / 2;
        }
    }

    function startLoop() {
        clearInterval(moveWavesId);
        moveWavesId = setInterval(moveWaves, 8000 / screenWidth);
    }

    function recalculateCanvas() {
        let containerInfo = container.getBoundingClientRect();

        screenWidth = containerInfo.width;
        screenHeight = containerInfo.height;

        htmlCanvas.width = screenWidth;
        htmlCanvas.height = screenHeight;

        wave = {};
        wave2 = {};
        wave3 = {};
        waveLength = 0;
        waves = {};

        startLoop();
    }

    window.addEventListener('resize', recalculateCanvas);
    window.addEventListener('orientationchange', recalculateCanvas);
    window.removeEventListener("unload", recalculateCanvas);

    recalculateCanvas();
    requestAnimationFrame(draw);
}());
