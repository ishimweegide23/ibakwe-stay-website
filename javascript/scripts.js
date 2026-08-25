// scripts.js

// Mobile Menu Toggle
document.addEventListener('DOMContentLoaded', () => {
    const menuToggle = document.querySelector('.menu-toggle');
    const navLinks = document.querySelector('.nav-links');
  
    if (menuToggle) {
      menuToggle.addEventListener('click', () => {
        navLinks.classList.toggle('active');
      });
    }
  });
  
  // Smooth Scroll for Navigation Links
  const navLinks = document.querySelectorAll('.nav-links a');
  navLinks.forEach(link => {
    link.addEventListener('click', (event) => {
      event.preventDefault();
      const targetId = link.getAttribute('href').substring(1);
      const targetElement = document.getElementById(targetId);
  
      if (targetElement) {
        window.scrollTo({
          top: targetElement.offsetTop - 60,
          behavior: 'smooth',
        });
      }
    });
  });
  
  // Form Validation
  const contactForm = document.querySelector('.contact-form form');
  if (contactForm) {
    contactForm.addEventListener('submit', (event) => {
      const name = document.getElementById('name').value.trim();
      const email = document.getElementById('email').value.trim();
      const message = document.getElementById('message').value.trim();
  
      if (!name || !email || !message) {
        event.preventDefault();
        alert('Please fill out all fields before submitting.');
      } else if (!validateEmail(email)) {
        event.preventDefault();
        alert('Please enter a valid email address.');
      }
    });
  }
  
  // Email Validation Function
  function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
  }
  
  // Back to Top Button
  const backToTopButton = document.createElement('button');
  backToTopButton.innerText = '⬆';
  backToTopButton.classList.add('back-to-top');
  document.body.appendChild(backToTopButton);
  
  window.addEventListener('scroll', () => {
    if (window.scrollY > 300) {
      backToTopButton.style.display = 'block';
    } else {
      backToTopButton.style.display = 'none';
    }
  });
  
  backToTopButton.addEventListener('click', () => {
    window.scrollTo({
      top: 0,
      behavior: 'smooth',
    });
  });
  