import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import HomePage from './pages/HomePage';
import RegistrationPage from './pages/RegistrationPage';
import PatientForm from './pages/PatientForm';
import ScoreForm from './pages/ScoreForm';
import DashboardPage from './pages/DashboardPage';

function App() {
  return (
      <Router>
        <Routes>
          <Route exact path="/" element={<HomePage />} />
          <Route path="/register" element={<RegistrationPage />} />
          <Route path="/patient" element={<PatientForm />} />
          <Route path="/score" element={<ScoreForm />} />
          <Route path="/dashboard" element={<DashboardPage />} />
        </Routes>
      </Router>
  );
}

export default App;
