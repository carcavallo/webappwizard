import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import HomePage from './pages/HomePage';
import RegistrationPage from './pages/RegistrationPage';
import PatientForm from './pages/PatientForm';
import EditPatientForm from './pages/EditPatientForm';
import ScoreForm from './pages/ScoreForm';
import EditScoreForm from './pages/EditScoreForm';
import ScoreDisplay from './pages/ScoreDisplay';
import DashboardPage from './pages/DashboardPage';
import TokenVerification from './components/TokenVerification';

function App() {
  return (
    <Router>
      <Routes>
        <Route exact path="/" element={<HomePage />} />
        <Route path="/register" element={<RegistrationPage />} />
        <Route
          path="/patient"
          element={
            <TokenVerification>
              <PatientForm />
            </TokenVerification>
          }
        />
        <Route
          path="/patient/:id/edit"
          element={
            <TokenVerification>
              <EditPatientForm />
            </TokenVerification>
          }
        />
        <Route
          path="/patient/:id/score"
          element={
            <TokenVerification>
              <ScoreForm />
            </TokenVerification>
          }
        />
        <Route
          path="/patient/:id/score/:score_id/edit"
          element={
            <TokenVerification>
              <EditScoreForm />
            </TokenVerification>
          }
        />
        <Route
          path="/patient/:id/score/:score_id/display"
          element={
            <TokenVerification>
              <ScoreDisplay />
            </TokenVerification>
          }
        />
        <Route
          path="/dashboard"
          element={
            <TokenVerification>
              <DashboardPage />
            </TokenVerification>
          }
        />
      </Routes>
    </Router>
  );
}

export default App;
