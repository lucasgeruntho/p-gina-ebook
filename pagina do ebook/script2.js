const carouselInner = document.getElementById('carouselInner');
const carousel = document.getElementById('carousel');
const nextBtn = document.querySelector('.next');
const prevBtn = document.querySelector('.prev');
const dots = document.querySelectorAll('.dot');

let cards = Array.from(document.querySelectorAll('.card-laranja'));
let currentIndex = 1;
let autoSlideInterval;
let isUserInteracting = false;

// Clone first and last cards
const firstClone = cards[0].cloneNode(true);
const lastClone = cards[cards.length - 1].cloneNode(true);

firstClone.classList.add('clone');
lastClone.classList.add('clone');

carouselInner.appendChild(firstClone);
carouselInner.insertBefore(lastClone, cards[0]);

cards = Array.from(document.querySelectorAll('.card-laranja')); // update list after cloning

const totalSlides = cards.length;

function showCard(index, animated = true) {
  const offset = -index * 100;

  if (animated) {
    carouselInner.style.transition = 'transform 0.5s ease';
  } else {
    carouselInner.style.transition = 'none';
  }

  carouselInner.style.transform = `translateX(${offset}%)`;

  // Dots (ignore clones)
  const dotIndex = (index - 1 + totalSlides - 2) % (totalSlides - 2);
  dots.forEach((dot, i) => {
    dot.classList.toggle('active', i === dotIndex);
  });
}

function nextCard() {
  currentIndex++;
  showCard(currentIndex);

  // Loop forward
  if (currentIndex === totalSlides - 1) {
    setTimeout(() => {
      currentIndex = 1;
      showCard(currentIndex, false);
    }, 500); // after transition ends
  }
}

function prevCard() {
  currentIndex--;
  showCard(currentIndex);

  // Loop backward
  if (currentIndex === 0) {
    setTimeout(() => {
      currentIndex = totalSlides - 2;
      showCard(currentIndex, false);
    }, 500);
  }
}

nextBtn.addEventListener('click', () => {
  isUserInteracting = true;
  nextCard();
});

prevBtn.addEventListener('click', () => {
  isUserInteracting = true;
  prevCard();
});

// Touch swipe
let startX = 0;

carousel.addEventListener('touchstart', e => {
  startX = e.touches[0].clientX;
  isUserInteracting = true;
});

carousel.addEventListener('touchend', e => {
  const endX = e.changedTouches[0].clientX;
  const diff = endX - startX;

  if (diff > 50) {
    prevCard();
  } else if (diff < -50) {
    nextCard();
  }
}); 

// Auto slide
function startAutoSlide() {
  autoSlideInterval = setInterval(() => {
    if (!isUserInteracting) nextCard();
    isUserInteracting = false;
  }, 3000);
}

carousel.addEventListener('mouseover', () => isUserInteracting = true);
carousel.addEventListener('mouseleave', () => isUserInteracting = false);

// Init
showCard(currentIndex, false);
startAutoSlide();





document.querySelectorAll('.meu-faq .faq-header').forEach(header => {
  header.addEventListener('click', () => {
    const item = header.parentElement;
    item.classList.toggle('active');
  });
});

 



