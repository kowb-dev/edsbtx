/**
 * Carousel Module with smooth seamless service blocks scrolling
 * Manages all carousels on the site
 */

export class CarouselModule {
    constructor() {
        this.carousels = new Map();
        this.servicesContainer = null;
        this.servicesWrapper = null;
        this.originalServices = [];
        this.totalServiceWidth = 0;
        this.serviceWidth = 0;
        this.isSeamlessScrolling = false;
        this.videoElement = null;
        this.isVideoLoaded = false;
        this.isMobile = this.isMobileDevice();

        this.scrollPosition = 0;
        this.rafId = null;
        this.isScrolling = false;
        this.scrollVelocity = 0;
        this.lastScrollTime = 0;

        window.carouselModule = this;

        this.init();
    }

    init() {
        this.initCarousels();
        this.initServices();
        this.initEventHandlers();
        this.initVideoBackground();
    }

    /* ==========================================================================
       Carousel Initialization
       ========================================================================== */

    initCarousels() {
        const carouselElements = document.querySelectorAll('.edsys-carousel');

        carouselElements.forEach(carousel => {
            const carouselId = carousel.id;
            if (!carouselId) return;

            const carouselData = {
                element: carousel,
                wrapper: carousel.querySelector('.edsys-carousel__wrapper'),
                slides: carousel.querySelectorAll('.edsys-carousel__slide'),
                currentSlide: 0,
                totalSlides: carousel.querySelectorAll('.edsys-carousel__slide').length,
                autoPlayInterval: null,
                isPaused: false
            };

            this.carousels.set(carouselId, carouselData);

            this.initNavigationButtons(carouselId);

            if (carouselId === 'mainCarousel') {
                this.startAutoPlay(carouselId);
                this.initAutoPlayControls(carouselId);
            }

            this.initTouchSupport(carouselId);
        });
    }

    initNavigationButtons(carouselId) {
        const carousel = this.carousels.get(carouselId);
        if (!carousel) return;

        const prevBtn = carousel.element.querySelector('.edsys-carousel__nav--prev');
        const nextBtn = carousel.element.querySelector('.edsys-carousel__nav--next');

        if (prevBtn) {
            prevBtn.addEventListener('click', () => this.previousSlide(carouselId));
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', () => this.nextSlide(carouselId));
        }
    }

    initAutoPlayControls(carouselId) {
        const carousel = this.carousels.get(carouselId);
        if (!carousel) return;

        if (!this.isMobile) {
            carousel.element.addEventListener('mouseenter', () => {
                carousel.isPaused = true;
                this.stopAutoPlay(carouselId);
            });

            carousel.element.addEventListener('mouseleave', () => {
                carousel.isPaused = false;
                this.startAutoPlay(carouselId);
            });
        }

        carousel.element.addEventListener('focusin', () => {
            carousel.isPaused = true;
            this.stopAutoPlay(carouselId);
        });

        carousel.element.addEventListener('focusout', () => {
            carousel.isPaused = false;
            this.startAutoPlay(carouselId);
        });
    }

    initTouchSupport(carouselId) {
        const carousel = this.carousels.get(carouselId);
        if (!carousel) return;

        let startX = 0;
        let startY = 0;
        let endX = 0;
        let endY = 0;
        let isSwiping = false;

        carousel.element.addEventListener('touchstart', (e) => {
            startX = e.touches[0].clientX;
            startY = e.touches[0].clientY;
            isSwiping = true;

            if (carouselId === 'mainCarousel') {
                this.stopAutoPlay(carouselId);
            }
        }, { passive: true });

        carousel.element.addEventListener('touchmove', (e) => {
            if (!isSwiping) return;

            endX = e.touches[0].clientX;
            endY = e.touches[0].clientY;

            const diffX = Math.abs(startX - endX);
            const diffY = Math.abs(startY - endY);

            if (diffY > diffX) {
                isSwiping = false;
            }
        }, { passive: true });

        carousel.element.addEventListener('touchend', (e) => {
            if (!isSwiping) return;

            endX = e.changedTouches[0].clientX;
            const difference = startX - endX;

            if (Math.abs(difference) > 50) {
                if (difference > 0) {
                    this.nextSlide(carouselId);
                } else {
                    this.previousSlide(carouselId);
                }
            }

            if (carouselId === 'mainCarousel' && !carousel.isPaused) {
                this.startAutoPlay(carouselId);
            }

            isSwiping = false;
        });
    }

    /* ==========================================================================
       Carousel Control Methods
       ========================================================================== */

    updateCarousel(carouselId) {
        const carousel = this.carousels.get(carouselId);
        if (!carousel) return;

        carousel.slides.forEach((slide, index) => {
            if (index === carousel.currentSlide) {
                slide.classList.add('edsys-carousel__slide--active');
            } else {
                slide.classList.remove('edsys-carousel__slide--active');
            }
        });

        carousel.element.dispatchEvent(new CustomEvent('slideChange', {
            detail: {
                currentSlide: carousel.currentSlide,
                totalSlides: carousel.totalSlides
            }
        }));

        if (carouselId === 'mainCarousel' && typeof gtag !== 'undefined') {
            gtag('event', 'carousel_slide_change', {
                slide_index: carousel.currentSlide,
                carousel_id: carouselId
            });
        }
    }

    goToSlide(carouselId, slideIndex) {
        const carousel = this.carousels.get(carouselId);
        if (!carousel) return;

        carousel.currentSlide = slideIndex;
        this.updateCarousel(carouselId);

        if (carouselId === 'mainCarousel') {
            this.restartAutoPlay(carouselId);
        }
    }

    nextSlide(carouselId) {
        const carousel = this.carousels.get(carouselId);
        if (!carousel) return;

        carousel.currentSlide = (carousel.currentSlide + 1) % carousel.totalSlides;
        this.updateCarousel(carouselId);

        if (carouselId === 'mainCarousel') {
            this.restartAutoPlay(carouselId);
        }
    }

    previousSlide(carouselId) {
        const carousel = this.carousels.get(carouselId);
        if (!carousel) return;

        carousel.currentSlide = carousel.currentSlide === 0 ? carousel.totalSlides - 1 : carousel.currentSlide - 1;
        this.updateCarousel(carouselId);

        if (carouselId === 'mainCarousel') {
            this.restartAutoPlay(carouselId);
        }
    }

    startAutoPlay(carouselId) {
        const carousel = this.carousels.get(carouselId);
        if (!carousel || carousel.autoPlayInterval || carousel.isPaused) return;

        carousel.autoPlayInterval = setInterval(() => {
            this.nextSlide(carouselId);
        }, 8000);
    }

    stopAutoPlay(carouselId) {
        const carousel = this.carousels.get(carouselId);
        if (!carousel || !carousel.autoPlayInterval) return;

        clearInterval(carousel.autoPlayInterval);
        carousel.autoPlayInterval = null;
    }

    restartAutoPlay(carouselId) {
        this.stopAutoPlay(carouselId);
        if (!this.carousels.get(carouselId).isPaused) {
            this.startAutoPlay(carouselId);
        }
    }

    /* ==========================================================================
       Video Background
       ========================================================================== */

    initVideoBackground() {
        this.videoElement = document.querySelector('.edsys-carousel__video');
        if (!this.videoElement) return;

        this.videoElement.addEventListener('loadeddata', () => {
            this.isVideoLoaded = true;
            this.videoElement.style.opacity = '1';
        });

        this.videoElement.addEventListener('canplaythrough', () => {
            this.isVideoLoaded = true;
        });

        this.videoElement.addEventListener('error', () => {
            this.handleVideoError();
        });

        this.videoElement.muted = true;

        if (this.isMobile) {
            this.optimizeVideoForMobile();
        }

        this.initVideoVisibilityControl();
    }

    handleVideoError() {
        const videoContainer = document.querySelector('.edsys-carousel__video-container');
        if (videoContainer) {
            videoContainer.style.backgroundImage = 'url("./img/hero-vid.jpg")';
            videoContainer.style.backgroundSize = 'cover';
            videoContainer.style.backgroundPosition = 'center';

            if (this.videoElement) {
                this.videoElement.style.display = 'none';
            }
        }
    }

    initVideoVisibilityControl() {
        document.addEventListener('visibilitychange', () => {
            if (!this.videoElement) return;

            if (document.hidden) {
                this.videoElement.pause();
            } else if (this.isVideoLoaded) {
                this.videoElement.play().catch(() => {});
            }
        });

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (!this.videoElement) return;

                if (entry.isIntersecting) {
                    if (this.isVideoLoaded) {
                        this.videoElement.play().catch(() => {});
                    }
                } else {
                    this.videoElement.pause();
                }
            });
        }, {
            threshold: 0.1
        });

        const heroSection = document.querySelector('.edsys-hero');
        if (heroSection) {
            observer.observe(heroSection);
        }
    }

    optimizeVideoForMobile() {
        if (!this.videoElement) return;

        if (this.isMobile) {
            this.videoElement.autoplay = false;
            this.videoElement.preload = 'metadata';
            this.videoElement.poster = './img/hero-vid.jpg';
        }
    }

    /* ==========================================================================
       Services Initialization
       ========================================================================== */

    initServices() {
        this.servicesContainer = document.getElementById('servicesContainer');
        if (!this.servicesContainer) return;

        this.originalServices = Array.from(this.servicesContainer.querySelectorAll('.edsys-service:not(.edsys-service--cloned)'));

        if (this.originalServices.length === 0) return;

        requestAnimationFrame(() => {
            this.calculateServiceDimensions();
            this.createSeamlessScroll();
            this.initServicesHandlers();

            if (!this.isMobile) {
                this.initServicesNavigation();
            }

            setTimeout(() => {
                if (!this.validateScrollPosition()) {
                    this.resetScrollPosition();
                }
            }, 100);
        });
    }

    calculateServiceDimensions() {
        if (this.originalServices.length === 0) return;

        const firstService = this.originalServices[0];
        const rect = firstService.getBoundingClientRect();
        const styles = window.getComputedStyle(firstService);

        this.serviceWidth = rect.width;

        const marginRight = parseFloat(styles.marginRight) ||
            (this.isMobile ?
                    parseFloat(getComputedStyle(document.documentElement).getPropertyValue('--card-gap-mobile')) || 16 :
                    parseFloat(getComputedStyle(document.documentElement).getPropertyValue('--grid-gap')) || 24
            );

        this.serviceWidth += marginRight;
        this.totalServiceWidth = this.serviceWidth * this.originalServices.length;
    }

    createSeamlessScroll() {
        const existingClones = this.servicesContainer.querySelectorAll('.edsys-service--cloned');
        existingClones.forEach(clone => clone.remove());

        const startFragment = document.createDocumentFragment();
        this.originalServices.forEach(service => {
            const clone = service.cloneNode(true);
            clone.classList.add('edsys-service--cloned', 'edsys-service--clone-start');
            startFragment.appendChild(clone);
        });

        this.servicesContainer.insertBefore(startFragment, this.servicesContainer.firstChild);

        const endFragment = document.createDocumentFragment();
        this.originalServices.forEach(service => {
            const clone = service.cloneNode(true);
            clone.classList.add('edsys-service--cloned', 'edsys-service--clone-end');
            endFragment.appendChild(clone);
        });

        this.servicesContainer.appendChild(endFragment);

        requestAnimationFrame(() => {
            const initialPosition = this.totalServiceWidth;
            this.servicesContainer.scrollLeft = initialPosition;
            this.scrollPosition = initialPosition;
        });
    }

    /* ==========================================================================
       Services Event Handlers
       ========================================================================== */

    initServicesHandlers() {
        let scrollTimeout;
        let lastScrollLeft = 0;
        let scrollVelocity = 0;
        let lastScrollTime = performance.now();

        this.servicesContainer.addEventListener('scroll', (e) => {
            const currentScrollLeft = this.servicesContainer.scrollLeft;
            const currentTime = performance.now();

            const timeDiff = currentTime - lastScrollTime;
            if (timeDiff > 0) {
                scrollVelocity = (currentScrollLeft - lastScrollLeft) / timeDiff;
            }

            const scrollDirection = currentScrollLeft > lastScrollLeft ? 1 : -1;

            if (Math.abs(scrollVelocity) < 2) {
                this.handleSeamlessScroll(scrollDirection);
            }

            lastScrollLeft = currentScrollLeft;
            lastScrollTime = currentTime;

            this.servicesContainer.classList.add('is-scrolling');

            clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(() => {
                this.servicesContainer.classList.remove('is-scrolling');
                this.isScrolling = false;
                scrollVelocity = 0;
            }, 150);
        }, { passive: true });

        this.initSmoothTouchScroll();
    }

    handleSeamlessScroll(direction = 0) {
        if (this.isSeamlessScrolling) return;

        const currentScroll = this.servicesContainer.scrollLeft;
        const containerWidth = this.servicesContainer.clientWidth;

        const startBoundary = this.serviceWidth * 0.5;
        const endBoundary = this.totalServiceWidth * 2 - this.serviceWidth * 0.5;

        if (currentScroll >= endBoundary && direction > 0) {
            this.performSeamlessJump(this.totalServiceWidth);
        }
        else if (currentScroll <= startBoundary && direction < 0) {
            const jumpToPosition = this.totalServiceWidth + (currentScroll - startBoundary);
            this.performSeamlessJump(jumpToPosition);
        }

        this.scrollPosition = currentScroll;
    }

    performSeamlessJump(newPosition) {
        this.isSeamlessScrolling = true;

        const originalBehavior = this.servicesContainer.style.scrollBehavior;
        this.servicesContainer.style.scrollBehavior = 'auto';

        requestAnimationFrame(() => {
            this.servicesContainer.scrollLeft = newPosition;
            this.scrollPosition = newPosition;

            requestAnimationFrame(() => {
                this.servicesContainer.style.scrollBehavior = originalBehavior;
                this.isSeamlessScrolling = false;
            });
        });
    }

    initSmoothTouchScroll() {
        if (!this.isMobile) return;

        let startX = 0;
        let startScrollLeft = 0;
        let isDragging = false;
        let startTime = 0;
        let velocityTracker = [];
        let lastX = 0;
        let lastTime = 0;

        const addVelocityPoint = (x, time) => {
            velocityTracker.push({ x, time });
            if (velocityTracker.length > 3) {
                velocityTracker.shift();
            }
        };

        const calculateVelocity = () => {
            if (velocityTracker.length < 2) return 0;

            const latest = velocityTracker[velocityTracker.length - 1];
            const oldest = velocityTracker[0];
            const timeDiff = latest.time - oldest.time;
            const distanceDiff = latest.x - oldest.x;

            return timeDiff > 0 ? distanceDiff / timeDiff : 0;
        };

        this.servicesContainer.addEventListener('touchstart', (e) => {
            const touch = e.touches[0];
            startX = touch.clientX;
            lastX = startX;
            startScrollLeft = this.servicesContainer.scrollLeft;
            isDragging = true;
            startTime = Date.now();
            lastTime = startTime;
            velocityTracker = [];

            this.servicesContainer.style.scrollBehavior = 'auto';

            e.preventDefault();

            addVelocityPoint(startX, startTime);
        }, { passive: false });

        this.servicesContainer.addEventListener('touchmove', (e) => {
            if (!isDragging) return;

            const touch = e.touches[0];
            const currentX = touch.clientX;
            const currentTime = Date.now();
            const diffX = startX - currentX;

            const newScrollLeft = startScrollLeft + diffX;
            this.servicesContainer.scrollLeft = newScrollLeft;

            if (currentTime - lastTime > 16) {
                addVelocityPoint(currentX, currentTime);
                lastX = currentX;
                lastTime = currentTime;
            }

            e.preventDefault();
        }, { passive: false });

        this.servicesContainer.addEventListener('touchend', (e) => {
            if (!isDragging) return;

            isDragging = false;
            const endTime = Date.now();
            const velocity = calculateVelocity();

            setTimeout(() => {
                this.servicesContainer.style.scrollBehavior = 'smooth';
            }, 16);

            if (Math.abs(velocity) > 0.3) {
                const direction = velocity > 0 ? -1 : 1;
                const momentum = Math.min(Math.abs(velocity) * 200, this.serviceWidth * 2);

                this.servicesContainer.scrollBy({
                    left: direction * momentum,
                    behavior: 'smooth'
                });
            }

            velocityTracker = [];
        }, { passive: true });

        this.servicesContainer.addEventListener('touchcancel', (e) => {
            isDragging = false;
            velocityTracker = [];
            this.servicesContainer.style.scrollBehavior = 'smooth';
        }, { passive: true });
    }

    initServicesNavigation() {
        if (this.isMobile) return;

        const prevBtn = document.querySelector('.edsys-services-nav--prev');
        const nextBtn = document.querySelector('.edsys-services-nav--next');

        if (prevBtn) {
            prevBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.scrollServices(-1);
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.scrollServices(1);
            });
        }
    }

    scrollServices(direction) {
        if (!this.servicesContainer) return;

        const scrollAmount = this.serviceWidth;

        this.servicesContainer.scrollBy({
            left: direction * scrollAmount,
            behavior: 'smooth'
        });
    }

    /* ==========================================================================
       Event Handlers
       ========================================================================== */

    initEventHandlers() {
        document.addEventListener('keydown', (e) => {
            const focusedCarousel = document.activeElement.closest('.edsys-carousel');
            if (!focusedCarousel) return;

            const carouselId = focusedCarousel.id;
            if (!carouselId) return;

            if (e.key === 'ArrowLeft') {
                e.preventDefault();
                this.previousSlide(carouselId);
            } else if (e.key === 'ArrowRight') {
                e.preventDefault();
                this.nextSlide(carouselId);
            } else if (e.key === ' ') {
                e.preventDefault();
                const carousel = this.carousels.get(carouselId);
                if (carousel) {
                    if (carousel.autoPlayInterval) {
                        this.stopAutoPlay(carouselId);
                        carousel.isPaused = true;
                    } else {
                        this.startAutoPlay(carouselId);
                        carousel.isPaused = false;
                    }
                }
            }
        });

        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                this.carousels.forEach((carousel, id) => {
                    if (carousel.autoPlayInterval) {
                        this.stopAutoPlay(id);
                    }
                });

                if (this.videoElement) {
                    this.videoElement.pause();
                }
            } else {
                this.carousels.forEach((carousel, id) => {
                    if (id === 'mainCarousel' && !carousel.isPaused) {
                        this.startAutoPlay(id);
                    }
                });

                if (this.videoElement && this.isVideoLoaded) {
                    this.videoElement.play().catch(() => {});
                }
            }
        });

        window.addEventListener('resize', () => {
            this.onResize();
        });

        if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            this.carousels.forEach((carousel, id) => {
                this.stopAutoPlay(id);
                carousel.isPaused = true;
            });
        }
    }

    /* ==========================================================================
       Utility Methods
       ========================================================================== */

    isMobileDevice() {
        return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ||
            window.innerWidth <= 1199;
    }

    onResize() {
        const wasMobile = this.isMobile;
        this.isMobile = this.isMobileDevice();

        if (wasMobile !== this.isMobile) {
            this.initServices();
        } else {
            this.calculateServiceDimensions();

            if (this.servicesContainer) {
                this.createSeamlessScroll();
            }
        }

        if (this.isMobile) {
            this.optimizeVideoForMobile();
        }
    }

    /* ==========================================================================
       Public API
       ========================================================================== */

    pause(carouselId) {
        const carousel = this.carousels.get(carouselId);
        if (carousel) {
            carousel.isPaused = true;
            this.stopAutoPlay(carouselId);
        }
    }

    play(carouselId) {
        const carousel = this.carousels.get(carouselId);
        if (carousel) {
            carousel.isPaused = false;
            this.startAutoPlay(carouselId);
        }
    }

    destroy(carouselId) {
        const carousel = this.carousels.get(carouselId);
        if (carousel) {
            this.stopAutoPlay(carouselId);
            this.carousels.delete(carouselId);
        }
    }

    playVideo() {
        if (this.videoElement && this.isVideoLoaded) {
            this.videoElement.play().catch(() => {});
        }
    }

    pauseVideo() {
        if (this.videoElement) {
            this.videoElement.pause();
        }
    }

    muteVideo() {
        if (this.videoElement) {
            this.videoElement.muted = true;
        }
    }

    unmuteVideo() {
        if (this.videoElement) {
            this.videoElement.muted = false;
        }
    }

    getCarouselState(carouselId) {
        const carousel = this.carousels.get(carouselId);
        if (!carousel) return null;

        return {
            currentSlide: carousel.currentSlide,
            totalSlides: carousel.totalSlides,
            isPlaying: !!carousel.autoPlayInterval,
            isPaused: carousel.isPaused
        };
    }

    getVideoState() {
        if (!this.videoElement) return null;

        return {
            isLoaded: this.isVideoLoaded,
            isPaused: this.videoElement.paused,
            isMuted: this.videoElement.muted,
            currentTime: this.videoElement.currentTime,
            duration: this.videoElement.duration
        };
    }

    scrollServicesToStart() {
        if (this.servicesContainer) {
            this.servicesContainer.scrollTo({
                left: this.totalServiceWidth,
                behavior: 'smooth'
            });
        }
    }

    scrollServicesToEnd() {
        if (this.servicesContainer) {
            this.servicesContainer.scrollTo({
                left: this.totalServiceWidth * 2 - this.servicesContainer.clientWidth,
                behavior: 'smooth'
            });
        }
    }

    getServicesState() {
        return {
            originalCount: this.originalServices.length,
            serviceWidth: this.serviceWidth,
            totalWidth: this.totalServiceWidth,
            currentScroll: this.servicesContainer?.scrollLeft || 0,
            containerWidth: this.servicesContainer?.clientWidth || 0,
            isSeamlessScrolling: this.isSeamlessScrolling,
            isMobile: this.isMobile,
            cloneStartCount: this.servicesContainer?.querySelectorAll('.edsys-service--clone-start').length || 0,
            cloneEndCount: this.servicesContainer?.querySelectorAll('.edsys-service--clone-end').length || 0,
            relativePosition: this.getCurrentServiceIndex()
        };
    }

    validateScrollPosition() {
        if (!this.servicesContainer || this.originalServices.length === 0) return false;

        const currentScroll = this.servicesContainer.scrollLeft;
        const containerWidth = this.servicesContainer.clientWidth;

        const firstOriginalPosition = this.totalServiceWidth;
        const isFirstElementVisible = currentScroll >= firstOriginalPosition - 10 &&
            currentScroll <= firstOriginalPosition + containerWidth;

        return isFirstElementVisible;
    }

    resetScrollPosition() {
        if (!this.servicesContainer) return;

        this.servicesContainer.scrollLeft = this.totalServiceWidth;
        this.scrollPosition = this.totalServiceWidth;
    }

    scrollToService(index) {
        if (!this.servicesContainer || index < 0 || index >= this.originalServices.length) {
            return;
        }

        const targetPosition = this.totalServiceWidth + (index * this.serviceWidth);

        this.servicesContainer.scrollTo({
            left: targetPosition,
            behavior: 'smooth'
        });
    }

    getCurrentServiceIndex() {
        if (!this.servicesContainer) return 0;

        const currentScroll = this.servicesContainer.scrollLeft;
        const relativeScroll = currentScroll - this.totalServiceWidth;
        const index = Math.round(relativeScroll / this.serviceWidth);

        return Math.max(0, Math.min(index, this.originalServices.length - 1));
    }

    cleanup() {
        this.carousels.forEach((carousel, id) => {
            this.stopAutoPlay(id);
        });

        if (this.rafId) {
            cancelAnimationFrame(this.rafId);
        }

        if (this.servicesContainer) {
            this.servicesContainer.removeEventListener('scroll', this.handleSeamlessScroll);
        }

        this.carousels.clear();
        this.servicesContainer = null;
        this.originalServices = [];
        this.videoElement = null;
    }
}