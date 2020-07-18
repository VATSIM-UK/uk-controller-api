import React from 'react';
import ReactDOM from 'react-dom';
import {
  BrowserRouter as Router,
  Switch,
  Route,
} from 'react-router-dom';
import { browserHistory } from 'react-router';
import Home from './components/Home';
import Faq from './components/Faq';
import Header from './components/Header';

function App() {
  return (
    <>
      <Router basename="/web" history={browserHistory}>
        <Header />
        <Switch>
          <Route exact path="/" component={Home} />
          <Route exact path="/help" component={Faq} />
        </Switch>
      </Router>
    </>
  );
}

export default App;

if (document.getElementById('ukcp-web-app')) {
  ReactDOM.render(<App />, document.getElementById('ukcp-web-app'));
}
