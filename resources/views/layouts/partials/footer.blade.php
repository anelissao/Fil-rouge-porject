<footer class="site-footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-section">
                <h3 class="footer-title">Debriefing.com</h3>
                <p>A collaborative learning platform for students and teachers to work together on projects and provide peer evaluations.</p>
            </div>
            
            <div class="footer-section">
                <h3 class="footer-title">Quick Links</h3>
                <ul class="footer-links">
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Contact</a></li>
                    <li><a href="#">FAQ</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3 class="footer-title">Contact</h3>
                <ul class="footer-contact">
                    <li><i class="fas fa-envelope"></i> info@debriefing.com</li>
                    <li><i class="fas fa-phone"></i> +212 5XX-XXXXXX</li>
                    <li><i class="fas fa-map-marker-alt"></i> Marrakech, Morocco</li>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <div class="copyright">
                &copy; {{ date('Y') }} Debriefing.com. All rights reserved.
            </div>
            <div class="legal-links">
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
            </div>
        </div>
    </div>
</footer>

<style>
    .site-footer {
        background-color: var(--highlight-color);
        padding: 3rem 0 1.5rem;
        margin-top: auto;
    }

    .footer-content {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 2rem;
        margin-bottom: 2rem;
    }

    .footer-title {
        color: var(--primary-color);
        font-size: 1.2rem;
        margin-bottom: 1rem;
        position: relative;
        padding-bottom: 0.5rem;
    }

    .footer-title::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: 0;
        width: 50px;
        height: 2px;
        background-color: var(--primary-color);
    }

    .footer-links, .footer-contact {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .footer-links li, .footer-contact li {
        margin-bottom: 0.5rem;
    }

    .footer-links a, .footer-contact li {
        color: var(--accent-color);
        text-decoration: none;
        transition: color 0.3s;
    }

    .footer-links a:hover {
        color: var(--primary-color);
    }

    .footer-bottom {
        border-top: 1px solid rgba(229, 231, 235, 0.1);
        padding-top: 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
    }

    .copyright {
        color: var(--accent-color);
        font-size: 0.9rem;
    }

    .legal-links {
        display: flex;
        gap: 1.5rem;
    }

    .legal-links a {
        color: var(--accent-color);
        text-decoration: none;
        font-size: 0.9rem;
        transition: color 0.3s;
    }

    .legal-links a:hover {
        color: var(--primary-color);
    }

    /* Responsive Styles */
    @media (max-width: 768px) {
        .footer-content {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        .footer-bottom {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }

        .legal-links {
            flex-direction: column;
            gap: 0.5rem;
        }
    }
</style> 