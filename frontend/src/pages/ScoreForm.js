import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import axios from 'axios';

const ScoreForm = () => {
  const navigate = useNavigate();
  const [scoreData, setScoreData] = useState({
    criteria_1: null, criteria_2: null, criteria_3: null, criteria_4: null,
    criteria_5: null, criteria_6: null, criteria_7: null, criteria_8: null,
    criteria_9: null, criteria_10: null, criteria_11: null, criteria_12: null,
    criteria_13: null, criteria_14: null, criteria_15: null, criteria_16: null,
    criteria_17: null, criteria_18: null, criteria_19: null, criteria_20: null,
  });

  const handleChange = (e) => {
    setScoreData({ ...scoreData, [e.target.name]: e.target.value });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      await axios.post('/api/score', scoreData);
      history.push('/score-display');
    } catch (error) {
      console.error(error);
    }
  };

  const renderCriteria = (number, question) => (
    <div className="form-group">
      <label>{question}</label>
      <div>
        <input type="radio" id={`criteria_${number}_yes`} name={`criteria_${number}`} value="yes" onChange={handleChange} />
        <label htmlFor={`criteria_${number}_yes`}>Ja</label>
      </div>
      <div>
        <input type="radio" id={`criteria_${number}_no`} name={`criteria_${number}`} value="no" onChange={handleChange} />
        <label htmlFor={`criteria_${number}_no`}>Nein</label>
      </div>
    </div>
  );

  return (
    <div className="container mt-5">
      <h1>Flip-Flop-Score Formular</h1>
      <form onSubmit={handleSubmit} className="mt-4">
        {renderCriteria(1, "Positive Eigenanamnese für Atopie")}
        {renderCriteria(2, "Bekannte Sensibilisierungen und/oder Nahrungsmittelunverträglichkeiten")}
        {renderCriteria(3, "Positive Familienanamnese für Atopie")}
        {renderCriteria(4, "Positive Familienanamnese für Psoriasis")}
        {renderCriteria(5, "Abrupte Exazerbation nach Absetzen einer systemischen Steroidtherapie")}
        {renderCriteria(6, "Gelenkschmerzen")}
        {renderCriteria(7, "Daktylitis und/oder Enthesiopathien")}
        {renderCriteria(8, "Dishydrosis")}
        {renderCriteria(9, "Pusteln")}
        {renderCriteria(10, "Psoriasis-typische Nagelveränderungen")}
        {renderCriteria(11, "Palmare Hyperlinearität")}
        {renderCriteria(12, "Kopfhautbefall vom Capillitium über die Stirn-Haargrenze hinaus")}
        {renderCriteria(13, "Dennie-Morgan-Falte u./od. periorbitale Verschattung u./od. halonierte Augen")}
        {renderCriteria(14, "Perlèche u./od. Cheilitis")}
        {renderCriteria(15, "Plaques-Befall der Retroaurikulärregion")}
        {renderCriteria(16, "Head Neck Dermatitis oder Dirty neck")}
        {renderCriteria(17, "Keratosis pilaris")}
        {renderCriteria(18, "Erythematosquamöse Plaques an Körper u./od. Extremitäten")}
        {renderCriteria(19, "Ekzeme u./od. Lichenifizierung der Beugen")}
        {renderCriteria(20, "Befall der Rima ani")}
        <button type="submit" className="btn btn-primary">Score Berechnen</button>
      </form>
    </div>
  );
};

export default ScoreForm;
