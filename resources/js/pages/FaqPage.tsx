import React from 'react';
import Faq from '../components/Faq';

const faqs = [
  {
    id: 1,
    title: 'FAQ 1',
    text: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean malesuada enim ac diam commodo gravida. Phasellus ut dui eget mi ultrices sodales imperdiet id ante. Praesent at porttitor orci.',
  },
  {
    id: 2,
    title: 'FAQ 2',
    text: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean malesuada enim ac diam commodo gravida. Phasellus ut dui eget mi ultrices sodales imperdiet id ante. Praesent at porttitor orci.',
  },
  {
    id: 3,
    title: 'FAQ 3',
    text: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean malesuada enim ac diam commodo gravida. Phasellus ut dui eget mi ultrices sodales imperdiet id ante. Praesent at porttitor orci.',
  },
  {
    id: 4,
    title: 'FAQ 4',
    text: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean malesuada enim ac diam commodo gravida. Phasellus ut dui eget mi ultrices sodales imperdiet id ante. Praesent at porttitor orci.',
  },
  {
    id: 5,
    title: 'FAQ 5',
    text: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean malesuada enim ac diam commodo gravida. Phasellus ut dui eget mi ultrices sodales imperdiet id ante. Praesent at porttitor orci.',
  },
  {
    id: 6,
    title: 'FAQ 6',
    text: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean malesuada enim ac diam commodo gravida. Phasellus ut dui eget mi ultrices sodales imperdiet id ante. Praesent at porttitor orci.',
  },
  {
    id: 7,
    title: 'FAQ 7',
    text: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean malesuada enim ac diam commodo gravida. Phasellus ut dui eget mi ultrices sodales imperdiet id ante. Praesent at porttitor orci.',
  },
  {
    id: 8,
    title: 'FAQ 8',
    text: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean malesuada enim ac diam commodo gravida. Phasellus ut dui eget mi ultrices sodales imperdiet id ante. Praesent at porttitor orci.',
  },
  {
    id: 9,
    title: 'FAQ 9',
    text: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean malesuada enim ac diam commodo gravida. Phasellus ut dui eget mi ultrices sodales imperdiet id ante. Praesent at porttitor orci.',
  },
  {
    id: 10,
    title: 'FAQ 10',
    text: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean malesuada enim ac diam commodo gravida. Phasellus ut dui eget mi ultrices sodales imperdiet id ante. Praesent at porttitor orci.',
  },
  {
    id: 11,
    title: 'FAQ 11',
    text: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean malesuada enim ac diam commodo gravida. Phasellus ut dui eget mi ultrices sodales imperdiet id ante. Praesent at porttitor orci.',
  },
  {
    id: 12,
    title: 'FAQ 12',
    text: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean malesuada enim ac diam commodo gravida. Phasellus ut dui eget mi ultrices sodales imperdiet id ante. Praesent at porttitor orci.',
  },
  {
    id: 13,
    title: 'FAQ 13',
    text: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean malesuada enim ac diam commodo gravida. Phasellus ut dui eget mi ultrices sodales imperdiet id ante. Praesent at porttitor orci.',
  },
  {
    id: 14,
    title: 'FAQ 14',
    text: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean malesuada enim ac diam commodo gravida. Phasellus ut dui eget mi ultrices sodales imperdiet id ante. Praesent at porttitor orci.',
  },
  {
    id: 15,
    title: 'FAQ 15',
    text: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean malesuada enim ac diam commodo gravida. Phasellus ut dui eget mi ultrices sodales imperdiet id ante. Praesent at porttitor orci.',
  },
  {
    id: 16,
    title: 'FAQ 16',
    text: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean malesuada enim ac diam commodo gravida. Phasellus ut dui eget mi ultrices sodales imperdiet id ante. Praesent at porttitor orci.',
  },
  {
    id: 17,
    title: 'FAQ 17',
    text: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean malesuada enim ac diam commodo gravida. Phasellus ut dui eget mi ultrices sodales imperdiet id ante. Praesent at porttitor orci.',
  },
  {
    id: 18,
    title: 'FAQ 18',
    text: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean malesuada enim ac diam commodo gravida. Phasellus ut dui eget mi ultrices sodales imperdiet id ante. Praesent at porttitor orci.',
  },
  {
    id: 19,
    title: 'FAQ 19',
    text: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean malesuada enim ac diam commodo gravida. Phasellus ut dui eget mi ultrices sodales imperdiet id ante. Praesent at porttitor orci.',
  },
];

const styles = {
  layout: {
    display: 'grid',
    gridTemplateRows: 'auto',
    gridTemplateColumns: '1fr 1fr 1fr',
    gridRowGap: '50px',
    gridColumnGap: '15px',
  },
};

const FaqPage = () => (
  <div style={styles.layout}>
    {faqs.map((faq) => <Faq key={faq.id} id={faq.id} title={faq.title} text={faq.text} />)}
  </div>
);

export default FaqPage;
