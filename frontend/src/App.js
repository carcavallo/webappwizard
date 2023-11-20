import { BrowserRouter as Router, Routes, Route} from 'react-router-dom';
import HomePage from './pages/HomePage';
import RegistrationPage from './pages/RegistrationPage';
import PatientForm from './pages/PatientForm';
import ScoreDisplay from './pages/ScoreDisplay';

function App() {
  return (
    <div>
    <Router>
      <Routes>
        <Route exact path="/" component={HomePage} />
        <Route path="/register" component={RegistrationPage} />
        <Route path="/patient-form" component={PatientForm} />
        <Route path="/score-display" component={ScoreDisplay} />
      </Routes>
    </Router>
    <h1>test</h1>
    </div>
  );
}

export default App;
