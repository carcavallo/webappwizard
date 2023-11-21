import React, { useState, useEffect } from 'react';
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
    jucken_letzte_24_stunden: '',
  });
  const [error, setError] = useState('');
  const [bisherigeTherapie, setBisherigeTherapie] = useState({
    lokaleTherapie: [],
    systemtherapie: [],
  });
  const [aktuelleTherapie, setAktuelleTherapie] = useState({
    lokaleTherapie: [],
    systemtherapie: [],
  });
  const [bisherigeTherapieSonstiges, setBisherigeTherapieSonstiges] = useState({
    lokaleTherapie: '',
    systemtherapie: '',
  });
  const [aktuelleTherapieSonstiges, setAktuelleTherapieSonstiges] = useState({
    lokaleTherapie: '',
    systemtherapie: '',
  });

  useEffect(() => {
    const fetchTherapyOptions = async () => {
      try {
        const token = localStorage.getItem('token');
        const headers = { Authorization: `Bearer ${token}` };

        const lokaleResponse = await axios.get('http://localhost/api/therapy/lokale', { headers });
        const systemtherapieResponse = await axios.get('http://localhost/api/therapy/systemtherapie', { headers });

        const mapOptions = (options) => options.map((option) => ({
          ...option,
          selected: false,
        }));

        setBisherigeTherapie({
          lokaleTherapie: mapOptions(lokaleResponse.data),
          systemtherapie: mapOptions(systemtherapieResponse.data),
        });

        setAktuelleTherapie({
          lokaleTherapie: mapOptions(lokaleResponse.data),
          systemtherapie: mapOptions(systemtherapieResponse.data),
        });
      } catch (error) {
        setError('Error fetching therapy options.');
      }
    };

    fetchTherapyOptions();
  }, []);
  
  const handleChange = (e) => {
    setPatientData({ ...patientData, [e.target.name]: e.target.value });
  };
  
  const handleCheckboxChange = (optionType, optionId, isAktuelle = false) => {
    const therapyOptionsToUpdate = isAktuelle ? aktuelleTherapie : bisherigeTherapie;
    const updatedTherapyOptions = { ...therapyOptionsToUpdate };
  
    const selectedOption = updatedTherapyOptions[optionType].find(
      (option) => option.id === optionId
    );
    if (selectedOption) {
      selectedOption.selected = !selectedOption.selected;
    }
  
    if (isAktuelle) {
      setAktuelleTherapie(updatedTherapyOptions);
    } else {
      setBisherigeTherapie(updatedTherapyOptions);
    }
  };
  
  const validateForm = () => {
    for (const [key, value] of Object.entries(patientData)) {
      if (key === 'histopathologie_ergebnis' && patientData.histopathologische_untersuchung !== 'Ja') {
        continue;
      }
      if (value === '') {
        setError('Bitte füllen Sie alle Felder aus.');
        return false;
      }
    }
    setError('');
    return true;
  };
  
  const updateTherapieData = async (patientId, therapieData, endpoint) => {
    const token = localStorage.getItem('token');
    await axios.post(`http://localhost/api/${endpoint}/${patientId}`, therapieData, {
      headers: { Authorization: `Bearer ${token}` },
    });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();

    if (!validateForm()) {
      return;
    }

    const doctorId = localStorage.getItem('userId');
    const patientPayload = {
      ...patientData,
      doctor_id: doctorId,
    };

    try {
      const token = localStorage.getItem('token');
      const createResponse = await axios.post('http://localhost/api/patient', patientPayload, {
        headers: { Authorization: `Bearer ${token}` },
      });

      const patientId = createResponse.data.patientId;

      const processTherapieOptions = async (therapyOptions, sonstiges, endpoint) => {
        const selectedTherapieIds = therapyOptions.filter(option => option.selected).map(option => option.id);
        const sonstigesData = therapyOptions.find(option => option.option_name === 'Sonstiges' && option.selected)?.option_name === 'Sonstiges' ? sonstiges : '';

        if (selectedTherapieIds.length > 0 || sonstigesData) {
          await updateTherapieData(patientId, {
            therapie_ids: selectedTherapieIds,
            sonstiges: sonstigesData
          }, endpoint);
        }
      };

      await processTherapieOptions(bisherigeTherapie.lokaleTherapie, bisherigeTherapieSonstiges.lokaleTherapie, 'patient-bisherige-therapien');
      await processTherapieOptions(bisherigeTherapie.systemtherapie, bisherigeTherapieSonstiges.systemtherapie, 'patient-bisherige-therapien');
      await processTherapieOptions(aktuelleTherapie.lokaleTherapie, aktuelleTherapieSonstiges.lokaleTherapie, 'patient-aktuelle-therapien');
      await processTherapieOptions(aktuelleTherapie.systemtherapie, aktuelleTherapieSonstiges.systemtherapie, 'patient-aktuelle-therapien');

      navigate('/dashboard');
    } catch (error) {
      setError(error.response?.data?.message || 'Ein Fehler ist aufgetreten.');
    }
  };
  
  return (
    <div className="container mt-5">
      <h1>Patienten registrieren</h1>
      {error && <div className="alert alert-danger" role="alert">{error}</div>}
      <form onSubmit={handleSubmit} className="mt-4">
        <div className="form-group mb-3">
          <label htmlFor="geburtsdatum">Geburtsdatum</label>
          <input type="date" className="form-control" id="geburtsdatum" name="geburtsdatum" value={patientData.geburtsdatum} onChange={handleChange} />
        </div>
  
        <div className="form-group mb-3">
          <label htmlFor="geschlecht">Geschlecht</label>
          <select className="form-control" id="geschlecht" name="geschlecht" value={patientData.geschlecht} onChange={handleChange}>
            <option value="">Geschlecht auswählen</option>
            <option value="Männlich">Männlich</option>
            <option value="Weiblich">Weiblich</option>
            <option value="Divers">Divers</option>
          </select>
        </div>
  
        <div className="form-group mb-3">
          <label htmlFor="ethnie">Ethnie</label>
          <input type="text" className="form-control" id="ethnie" name="ethnie" value={patientData.ethnie} onChange={handleChange} />
        </div>
  
        <div className="form-group mb-3">
          <label htmlFor="vermutete_diagnose">Vermutete Diagnose</label>
          <select className="form-control" id="vermutete_diagnose" name="vermutete_diagnose" value={patientData.vermutete_diagnose} onChange={handleChange}>
            <option value="">Diagnose auswählen</option>
            <option value="AD">AD</option>
            <option value="Psoriasis">Psoriasis</option>
            <option value="Flip-Flop">Flip-Flop</option>
          </select>
        </div>
  
        <div className="form-group mb-3">
          <label htmlFor="histopathologische_untersuchung">Histopathologische Untersuchung</label>
          <select className="form-control" id="histopathologische_untersuchung" name="histopathologische_untersuchung" value={patientData.histopathologische_untersuchung} onChange={handleChange}>
            <option value="">Option auswählen</option>
            <option value="Ja">Ja</option>
            <option value="Nein">Nein</option>
          </select>
        </div>
  
        {patientData.histopathologische_untersuchung === 'Ja' && (
          <div className="form-group mb-3">
            <label htmlFor="histopathologie_ergebnis">Histopathologie Ergebnis</label>
            <input className="form-control" id="histopathologie_ergebnis" name="histopathologie_ergebnis" value={patientData.histopathologie_ergebnis} onChange={handleChange}></input>
          </div>
        )}
  
        <div className="form-group mb-3">
          <label htmlFor="bisherige_lokaltherapie">Bisherige lokale Therapie</label>
          {bisherigeTherapie.lokaleTherapie.map((option) => (
            <div key={option.id} className="form-check">
              <input
                className="form-check-input"
                type="checkbox"
                id={`bisherige_lokaltherapie_${option.id}`}
                checked={option.selected}
                onChange={() => handleCheckboxChange('lokaleTherapie', option.id)}
              />
              <label className="form-check-label" htmlFor={`bisherige_lokaltherapie_${option.id}`}>
                {option.option_name}
              </label>
              {option.option_name === 'Sonstiges' && option.selected && (
                <input
                  type="text"
                  className="form-control"
                  placeholder="Sonstiges"
                  value={bisherigeTherapieSonstiges.lokaleTherapie}
                  onChange={(e) =>
                    setBisherigeTherapieSonstiges({
                      ...bisherigeTherapieSonstiges,
                      lokaleTherapie: e.target.value,
                    })
                  }
                />
              )}
            </div>
          ))}
        </div>
  
        <div className="form-group mb-3">
          <label htmlFor="bisherige_systemtherapie">Bisherige systemische Therapie</label>
          {bisherigeTherapie.systemtherapie.map((option) => (
            <div key={option.id} className="form-check">
              <input
                className="form-check-input"
                type="checkbox"
                id={`bisherige_systemtherapie_${option.id}`}
                checked={option.selected}
                onChange={() => handleCheckboxChange('systemtherapie', option.id)}
              />
              <label className="form-check-label" htmlFor={`bisherige_systemtherapie_${option.id}`}>
                {option.option_name}
              </label>
              {option.option_name === 'Sonstiges' && option.selected && (
                <input
                  type="text"
                  className="form-control"
                  placeholder="Sonstiges"
                  value={bisherigeTherapieSonstiges.systemtherapie}
                  onChange={(e) =>
                    setBisherigeTherapieSonstiges({
                      ...bisherigeTherapieSonstiges,
                      systemtherapie: e.target.value,
                    })
                  }
                />
              )}
            </div>
          ))}
        </div>
  
        <div className="form-group mb-3">
          <label htmlFor="aktuelle_lokaltherapie">Aktuelle lokale Therapie</label>
          {aktuelleTherapie.lokaleTherapie.map((option) => (
            <div key={option.id} className="form-check">
              <input
                className="form-check-input"
                type="checkbox"
                id={`aktuelle_lokaltherapie_${option.id}`}
                checked={option.selected}
                onChange={() => handleCheckboxChange('lokaleTherapie', option.id, true)}
              />
              <label className="form-check-label" htmlFor={`aktuelle_lokaltherapie_${option.id}`}>
                {option.option_name}
              </label>
              {option.option_name === 'Sonstiges' && option.selected && (
                <input
                  type="text"
                  className="form-control"
                  placeholder="Sonstiges"
                  value={aktuelleTherapieSonstiges.lokaleTherapie}
                  onChange={(e) =>
                    setAktuelleTherapieSonstiges({
                      ...aktuelleTherapieSonstiges,
                      lokaleTherapie: e.target.value,
                    })
                  }
                />
              )}
            </div>
          ))}
        </div>
  
        <div className="form-group mb-3">
          <label htmlFor="aktuelle_systemtherapie">Aktuelle systemische Therapie</label>
          {aktuelleTherapie.systemtherapie.map((option) => (
            <div key={option.id} className="form-check">
              <input
                className="form-check-input"
                type="checkbox"
                id={`aktuelle_systemtherapie_${option.id}`}
                checked={option.selected}
                onChange={() => handleCheckboxChange('systemtherapie', option.id, true)}
              />
              <label className="form-check-label" htmlFor={`aktuelle_systemtherapie_${option.id}`}>
                {option.option_name}
              </label>
              {option.option_name === 'Sonstiges' && option.selected && (
                <input
                  type="text"
                  className="form-control"
                  placeholder="Sonstiges"
                  value={aktuelleTherapieSonstiges.systemtherapie}
                  onChange={(e) =>
                    setAktuelleTherapieSonstiges({
                      ...aktuelleTherapieSonstiges,
                      systemtherapie: e.target.value,
                    })
                  }
                />
              )}
            </div>
          ))}
        </div>

        <div className="form-group mb-3">
          <label htmlFor="jucken_letzte_24_stunden">Jucken in den letzten 24 Stunden</label>
          <select className="form-control" id="jucken_letzte_24_stunden" name="jucken_letzte_24_stunden" value={patientData.jucken_letzte_24_stunden} onChange={handleChange}>
            <option value="">Intensität auswählen</option>
            {[...Array(11).keys()].map((num) => (
              <option key={num} value={num}>
                {num === 0 ? '0 (gar kein Jucken)' : num === 10 ? '10 (schwerstes vorstellbares Jucken)' : num}
              </option>
            ))}
          </select>
        </div>
  
        <button type="submit" className="btn btn-primary mb-3">Patientendaten Speichern</button>
      </form>
    </div>
  );
};

export default PatientForm;
