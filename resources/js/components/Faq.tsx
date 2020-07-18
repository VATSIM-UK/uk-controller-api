import React from 'react';

type MapKindToComponentT = {
  [key: string]: React.SFC<any>
};

interface FaqProps {
  id: number;
  title: string;
  text: string
}

const styles = {
  title: {
    background: '#17375e',
    color: 'white',
  },
  content: {
    background: 'white',
  },
};

const Faq: React.SFC<any> = ({ id, title, text }: FaqProps) => (
  <div name={`faq${id}`} style={styles.content}>
    <h4 style={styles.title}>{title}</h4>
    <div>{text}</div>
  </div>
);

export default Faq;
