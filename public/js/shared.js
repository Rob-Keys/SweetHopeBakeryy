class ImageSlider {
  constructor(containerSelector) {
    this.container = typeof containerSelector === 'string' 
      ? document.querySelector(containerSelector) 
      : containerSelector;
    this.wrapper = this.container.querySelector('.slider-wrapper');
    this.slides = this.container.querySelectorAll('.slide');
    this.leftArrow = this.container.querySelector('.arrow.left');
    this.rightArrow = this.container.querySelector('.arrow.right');
    
    this.currentIndex = 0;
    this.totalSlides = this.slides.length;
    
    this.init();
  }
  
  init() {
    if(this.leftArrow && this.rightArrow) {
      this.leftArrow.addEventListener('click', () => this.prevSlide());
      this.rightArrow.addEventListener('click', () => this.nextSlide());
    }
  }
  
  updateSlider() {
    const offset = -this.currentIndex * 100;
    this.wrapper.style.transform = `translateX(${offset}%)`;
  }
  
  nextSlide() {
    this.currentIndex = (this.currentIndex + 1) % this.totalSlides;
    this.updateSlider();
  }
  
  prevSlide() {
    this.currentIndex = (this.currentIndex - 1 + this.totalSlides) % this.totalSlides;
    this.updateSlider();
  }
}

function add_image_slider(){
  const sliders = document.querySelectorAll('.slider-container');
	sliders.forEach(slider => {
		new ImageSlider(slider);
	});
}

function add_fade_in_effects(){
    const elements = document.querySelectorAll('.fade-in-up, .fade-in-right, .fade-in-left');

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('fade-visible');
      }
    });
  }, {
    threshold: 0.2 // Trigger when 20% of the element is visible
  });

  elements.forEach(el => observer.observe(el));
}

document.addEventListener('DOMContentLoaded', () => {
  add_image_slider();
  add_fade_in_effects();
});