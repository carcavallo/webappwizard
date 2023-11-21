import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import HomePage from './pages/HomePage';
import RegistrationPage from './pages/RegistrationPage';
import PatientForm from './pages/PatientForm';
import ScoreForm from './pages/ScoreForm';
import ScoreDisplay from './pages/ScoreDisplay';
import DashboardPage from './pages/DashboardPage';

function App() {
  return (
    <Router>
      <Routes>
        <Route exact path="/" element={<HomePage />} />
        <Route path="/register" element={<RegistrationPage />} />
        <Route path="/patient" element={<PatientForm />} />
        <Route path="/patient/:id/score" element={<ScoreForm />} />
        <Route path="/patient/:id/score/display" element={<ScoreDisplay />} />
        <Route path="/dashboard" element={<DashboardPage />} />
      </Routes>
    </Router>
  );
}

export default App;
