import React from 'react';
import {
  Link,
} from 'react-router-dom';

const styles = {
  header: {
    position: 'sticky',
    minHeight: '55px',
    background: '#17375e',
    marginBottom: '20px',
    border: '1px solid transparent',
    borderBottom: '5px solid #00b0f0',
    right: '0',
    left: '0',
    top: '0',
    zIndex: '1030',
    padding: '16px, 15px',
  },
  links: {
    color: 'white',
    fontSize: '1.25rem',
    textDecoration: 'none',
  },
  homepage: {
    lineHeight: '20px',
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
