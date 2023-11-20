import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import axios from 'axios';

const PatientForm = () => {
  const navigate = useNavigate();
  const [patientData, setPatientData] = useState({
    geburtsdatum: '',
    geschlecht: '',
    ethnie: '',
    vermutete_diagnose: '',
    histopathologische_untersuchung: '',
    histopathologie_ergebnis: '',
    bisherige_lokaltherapie_sonstiges: '',
    bisherige_systemtherapie_sonstiges: '',
    aktuelle_lokaltherapie_sonstiges: '',
    aktuelle_systemtherapie_sonstiges: '',
    jucken_letzte_24_stunden: '',
  });
  const [error, setError] = useState('');

  const handleChange = (e) => {
    setPatientData({ ...patientData, [e.target.name]: e.target.value });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      await axios.post('/api/patient', patientData);
      navigate.push('/dashboard');
    } catch (error) {
      setError(error.response?.data?.message || 'An error occurred');
    }
  };

  return (
    <div className="container mt-5">
      <h1>Patient Data Entry</h1>
      {error && <div className="alert alert-danger" role="alert">{error}</div>}
      <form onSubmit={handleSubmit} className="mt-4">
        <div className="form-group">
          <label htmlFor="geburtsdatum">Birth Date</label>
          <input type="date" className="form-control" id="geburtsdatum" name="geburtsdatum" value={patientData.geburtsdatum} onChange={handleChange} />
        </div>

        <div className="form-group">
          <label htmlFor="geschlecht">Gender</label>
          <select className="form-control" id="geschlecht" name="geschlecht" value={patientData.geschlecht} onChange={handleChange}>
            <option value="">Select Gender</option>
            <option value="Männlich">Male</option>
            <option value="Weiblich">Female</option>
            <option value="Divers">Diverse</option>
          </select>
        </div>

        <div className="form-group">
          <label htmlFor="ethnie">Ethnicity</label>
          <input type="text" className="form-control" id="ethnie" name="ethnie" value={patientData.ethnie} onChange={handleChange} />
        </div>

        <div className="form-group">
          <label htmlFor="vermutete_diagnose">Suspected Diagnosis</label>
          <select className="form-control" id="vermutete_diagnose" name="vermutete_diagnose" value={patientData.vermutete_diagnose} onChange={handleChange}>
            <option value="">Select Diagnosis</option>
            <option value="AD">AD</option>
            <option value="Psoriasis">Psoriasis</option>
            <option value="Flip-Flop">Flip-Flop</option>
          </select>
        </div>

        <div className="form-group">
          <label htmlFor="histopathologische_untersuchung">Histopathological Examination</label>
          <select className="form-control" id="histopathologische_untersuchung" name="histopathologische_untersuchung" value={patientData.histopathologische_untersuchung} onChange={handleChange}>
            <option value="">Select Option</option>
            <option value="Ja">Yes</option>
            <option value="Nein">No</option>
          </select>
        </div>

        {patientData.histopathologische_untersuchung === 'Ja' && (
          <div className="form-group">
            <label htmlFor="histopathologie_ergebnis">Histopathology Result</label>
            <textarea className="form-control" id="histopathologie_ergebnis" name="histopathologie_ergebnis" value={patientData.histopathologie_ergebnis} onChange={handleChange}></textarea>
          </div>
        )}

        <div className="form-group">
          <label htmlFor="bisherige_lokaltherapie_sonstiges">Previous Local Therapy</label>
          <input type="text" className="form-control" id="bisherige_lokaltherapie_sonstiges" name="bisherige_lokaltherapie_sonstiges" value={patientData.bisherige_lokaltherapie_sonstiges} onChange={handleChange} />
        </div>

        <div className="form-group">
          <label htmlFor="bisherige_systemtherapie_sonstiges">Previous Systemic Therapy</label>
          <input type="text" className="form-control" id="bisherige_systemtherapie_sonstiges" name="bisherige_systemtherapie_sonstiges" value={patientData.bisherige_systemtherapie_sonstiges} onChange={handleChange} />
        </div>

        <div className="form-group">
          <label htmlFor="aktuelle_lokaltherapie_sonstiges">Current Local Therapy</label>
          <input type="text" className="form-control" id="aktuelle_lokaltherapie_sonstiges" name="aktuelle_lokaltherapie_sonstiges" value={patientData.aktuelle_lokaltherapie_sonstiges} onChange={handleChange} />
        </div>

        <div className="form-group">
          <label htmlFor="aktuelle_systemtherapie_sonstiges">Current Systemic Therapy</label>
          <input type="text" className="form-control" id="aktuelle_systemtherapie_sonstiges" name="aktuelle_systemtherapie_sonstiges" value={patientData.aktuelle_systemtherapie_sonstiges} onChange={handleChange} />
        </div>

        <div className="form-group">
          <label htmlFor="jucken_letzte_24_stunden">Itching in the last 24 hours</label>
          <select className="form-control" id="jucken_letzte_24_stunden" name="jucken_letzte_24_stunden" value={patientData.jucken_letzte_24_stunden} onChange={handleChange}>
            <option value="">Select Intensity</option>
            {[...Array(11).keys()].map((num) => (
              <option key={num} value={num}>{num}</option>
            ))}
          </select>
        </div>

        <button type="submit" className="btn btn-primary">Save Patient Data</button>
      </form>
    </div>
  );
};

export default PatientForm;
