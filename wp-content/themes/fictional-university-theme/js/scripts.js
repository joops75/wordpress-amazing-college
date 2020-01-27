// 3rd party packages from NPM
import $ from 'jquery';
import slick from 'slick-carousel';

// Our modules / classes
import MobileMenu from './modules/MobileMenu';
import HeroSlider from './modules/HeroSlider';
import Search from './modules/Search';
import MyNotes from './modules/MyNotes';
import Like from './modules/Like';

// Instantiate a new object using our modules/classes
var mobileMenu = new MobileMenu();
var heroSlider = new HeroSlider();
var realTimeSearch = new Search();
var notes = new MyNotes();
var like = new Like();