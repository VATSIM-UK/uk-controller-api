import React from 'react';
import ReactDOM from 'react-dom';
import {
  BrowserRouter as Router,
  Switch,
  Route,
} from 'react-router-dom';
import { browserHistory } from 'react-router';
import Home from './components/Home';
import FaqPage from './pages/FaqPage';
import Header from './components/Header';

const styles = {
  page: {
    display: 'grid',
    gridTemplateRows: '50px auto',
    gridTemplateColumns: '1fr',
    height: '100vh',
  },
};

function App() {
  return (
    <div styles={styles.page}>
      <Router basename="/web" history={browserHistory}>
        <Header />
        <Switch>
          <Route exact path="/" component={Home} />
          <Route exact path="/help" component={FaqPage} />
        </Switch>
      </Router>
    </div>
  );
}

export default App;

if (document.getElementById('ukcp-web-app')) {
  ReactDOM.render(<App />, document.getElementById('ukcp-web-app'));
}
