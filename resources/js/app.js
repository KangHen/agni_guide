import './bootstrap';
import Quill from 'quill';
import mapboxgl from 'mapbox-gl';
import Chart from 'chart.js/auto';
import 'mapbox-gl/dist/mapbox-gl.css';
import anime from 'animejs/lib/anime.es.js';
import 'animate.css';

window.Quill = Quill;
window.mapboxgl = mapboxgl;
window.Chart = Chart;
window.anime = anime;

const createAnimation = () => {
    console.log('Creating animation');
    anime({
        targets: '.head-title',
        translateX: 350,
        scale: 1.5,
        rotate: '1turn',
        delay: 1000,
        duration:2000
    });
}

window.createAnimation = createAnimation;
