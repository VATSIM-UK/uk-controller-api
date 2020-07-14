import React from 'react';
import ReactDOM from 'react-dom';
import {
    BrowserRouter as Router,
    Switch,
    Route,
    Link
} from "react-router-dom";
import Home from "./components/Home";
import Faq from "./components/Faq";

function App() {
    return (
        <Router basename="/web">
            <Switch>
                <Route exact path="/" component={Home} />
                <Route exact path="/faq" component={Faq} />
            </Switch>
        </Router>
    );
}

export default App;

if (document.getElementById('ukcp-web-app')) {
    ReactDOM.render(<App />, document.getElementById('ukcp-web-app'));
}
