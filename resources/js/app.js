import './bootstrap';
import './timeskip-listener';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Aktionmarkt Welcome Page Animations
document.addEventListener('DOMContentLoaded', function() {
    
    // Typewriter Effect
    const typewriterElement = document.getElementById('typewriter');
    if (typewriterElement) {
        const text = "Willkommen bei Aktionmarkt";
        let i = 0;
        
        function typeWriter() {
            if (i < text.length) {
                typewriterElement.innerHTML += text.charAt(i);
                i++;
                setTimeout(typeWriter, 100);
            }
        }
        
        // Start typewriter effect after a delay
        setTimeout(typeWriter, 500);
    }
    
    // Smooth Scrolling for Navigation Links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Scroll-triggered animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('reveal', 'active');
            }
        });
    }, observerOptions);
    
    // Observe feature cards and stat cards
    document.querySelectorAll('.feature-card, .stat-card').forEach(card => {
        card.classList.add('reveal');
        observer.observe(card);
    });
    
    // Animated Statistics Counter
    function animateCounter(element, target) {
        let current = 0;
        const increment = target / 100;
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            
            // Format number based on target value
            if (target >= 1000) {
                element.textContent = Math.floor(current).toLocaleString();
            } else {
                element.textContent = Math.floor(current);
            }
        }, 20);
    }
    
    // Trigger counter animation when stats section is visible
    const statsObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const statNumbers = entry.target.querySelectorAll('.stat-number');
                statNumbers.forEach(stat => {
                    const target = parseInt(stat.dataset.target);
                    animateCounter(stat, target);
                });
                statsObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });
    
    const statsSection = document.getElementById('stats');
    if (statsSection) {
        statsObserver.observe(statsSection);
    }
    
    // Navigation Background on Scroll
    const nav = document.querySelector('nav');
    if (nav) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 100) {
                nav.classList.add('scrolled');
            } else {
                nav.classList.remove('scrolled');
            }
        });
    }
    
    // Parallax Effect for Background Elements
    window.addEventListener('scroll', () => {
        const scrolled = window.pageYOffset;
        const parallaxElements = document.querySelectorAll('.absolute');
        
        parallaxElements.forEach((element, index) => {
            const speed = 0.5 + (index * 0.1);
            element.style.transform = `translateY(${scrolled * speed}px)`;
        });
    });
    
    // Mouse Movement Parallax
    document.addEventListener('mousemove', (e) => {
        const mouseX = e.clientX / window.innerWidth;
        const mouseY = e.clientY / window.innerHeight;
        
        // Move background elements slightly with mouse
        const floatingElements = document.querySelectorAll('.absolute.top-20, .absolute.top-40, .absolute.bottom-20');
        floatingElements.forEach((element, index) => {
            const moveX = (mouseX - 0.5) * (20 + index * 10);
            const moveY = (mouseY - 0.5) * (20 + index * 10);
            element.style.transform = `translate(${moveX}px, ${moveY}px)`;
        });
    });
    
    // Enhanced Button Ripple Effect
    document.querySelectorAll('.group').forEach(button => {
        button.addEventListener('click', function(e) {
            const ripple = this.querySelector('.ripple');
            if (ripple) {
                const rect = this.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                
                ripple.style.left = x + 'px';
                ripple.style.top = y + 'px';
                ripple.style.width = '0px';
                ripple.style.height = '0px';
                
                // Trigger ripple animation
                setTimeout(() => {
                    ripple.style.width = '300px';
                    ripple.style.height = '300px';
                }, 10);
                
                // Reset ripple
                setTimeout(() => {
                    ripple.style.width = '0px';
                    ripple.style.height = '0px';
                }, 600);
            }
        });
    });
    
    // Dynamic Particle Creation (Enhanced)
    function createFloatingParticle() {
        const particle = document.createElement('div');
        particle.className = 'particle';
        particle.style.cssText = `
            position: absolute;
            width: ${Math.random() * 6 + 2}px;
            height: ${Math.random() * 6 + 2}px;
            background: rgba(255, 255, 255, ${Math.random() * 0.3 + 0.1});
            border-radius: 50%;
            pointer-events: none;
            top: 100vh;
            left: ${Math.random() * 100}%;
            animation: float ${Math.random() * 10 + 8}s infinite linear;
        `;
        
        document.body.appendChild(particle);
        
        // Remove particle after animation
        setTimeout(() => {
            if (particle.parentNode) {
                particle.parentNode.removeChild(particle);
            }
        }, 18000);
    }
    
    // Add floating animation keyframes dynamically
    if (!document.querySelector('#dynamic-animations')) {
        const style = document.createElement('style');
        style.id = 'dynamic-animations';
        style.textContent = `
            @keyframes float {
                0% {
                    transform: translateY(0px) translateX(0px);
                    opacity: 0;
                }
                10% {
                    opacity: 1;
                }
                90% {
                    opacity: 1;
                }
                100% {
                    transform: translateY(-100vh) translateX(${Math.random() * 200 - 100}px);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    }
    
    // Create particles periodically
    setInterval(createFloatingParticle, 2000);
    
    // Loading Animation for Page
    window.addEventListener('load', () => {
        document.body.classList.add('loaded');
        
        // Add loading complete class to trigger entrance animations
        const heroSection = document.querySelector('section');
        if (heroSection) {
            heroSection.classList.add('animate-in');
        }
    });
    
    // Enhanced Hover Effects for Cards
    document.querySelectorAll('.feature-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px) scale(1.02)';
            this.style.boxShadow = '0 20px 40px rgba(0, 0, 0, 0.3)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
            this.style.boxShadow = 'none';
        });
    });
    
    // Keyboard Navigation Enhancement
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Tab') {
            document.body.classList.add('keyboard-navigation');
        }
    });
    
    document.addEventListener('mousedown', () => {
        document.body.classList.remove('keyboard-navigation');
    });
    
    // Performance Optimization: Throttle Scroll Events
    let ticking = false;
    
    function updateOnScroll() {
        // Update scroll-dependent animations here
        ticking = false;
    }
    
    window.addEventListener('scroll', () => {
        if (!ticking) {
            requestAnimationFrame(updateOnScroll);
            ticking = true;
        }
    });
    
    // Accessibility: Reduced Motion Support
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    
    function handleReducedMotion(e) {
        if (e.matches) {
            // Disable animations for users who prefer reduced motion
            document.documentElement.style.setProperty('--animation-duration', '0.01ms');
        } else {
            document.documentElement.style.removeProperty('--animation-duration');
        }
    }
    
    prefersReducedMotion.addListener(handleReducedMotion);
    handleReducedMotion(prefersReducedMotion);
    
    // Add entrance animations to elements
    const entranceElements = document.querySelectorAll('h1, h2, p, .feature-card, .stat-card');
    entranceElements.forEach((el, index) => {
        el.style.animationDelay = `${index * 0.1}s`;
        el.classList.add('animate-entrance');
    });
    
    console.log('🎉 Aktionmarkt Welcome Page Animations Loaded!');
});
