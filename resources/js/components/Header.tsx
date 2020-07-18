import React from 'react';
import {
  Link,
} from 'react-router-dom';

const styles = {
  header: {
    'min-height': '55px',
    background: '#17375e',
    'margin-bottom': '20px',
    border: '1px solid transparent',
    'border-bottom': '5px solid #00b0f0',
    position: 'fixed',
    right: '0',
    left: '0',
    top: '0',
    'z-index': '1030',
    padding: '16px, 15px',
  },
  links: {
    color: 'white',
    'font-size': '1.25rem',
    'text-decoration': 'none',
  },
  homepage: {
    'line-height': '20px',
    padding: '5px',
  },
  logo: {
    height: '50px',
  },
};

const Header = () => (
  <div style={styles.header}>
    <a href="https://www.vatsim.uk" style={styles.homepage}>
      <img style={styles.logo} src="https://www.vatsim.uk/images/vatsim_uk_logo.png" alt="VATSIM UK Logo" />
    </a>
    <Link style={styles.links} to="/">Home</Link>
    <Link style={styles.links} to="/help">FAQs</Link>
  </div>
);

export default Header;
