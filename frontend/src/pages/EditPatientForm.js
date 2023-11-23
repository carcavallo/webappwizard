import React, { useState, useEffect, useCallback } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import axios from 'axios';
import NavBar from '../components/Navigation';

const PatientForm = () => {
  const { id: patientId } = useParams();
  const navigate = useNavigate();
  const [isLoading, setIsLoading] = useState(true);
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
  const [bisherigeTherapieValues, setBisherigeTherapieValues] = useState({
    lokaleTherapie: [],
    systemtherapie: [],
  });
  const [aktuelleTherapieValues, setAktuelleTherapieValues] = useState({
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

  const extractTherapyIds = therapyData => {
    return therapyData.flatMap(obj => Object.values(obj));
  };
  const fetchTherapyOptions = useCallback(async () => {
    try {
      const token = localStorage.getItem('token');
      const headers = { Authorization: `Bearer ${token}` };

      const lokaleResponse = await axios.get(
        'http://localhost/api/therapy/lokale',
        { headers }
      );
      const systemtherapieResponse = await axios.get(
        'http://localhost/api/therapy/systemtherapie',
        { headers }
      );

      const mapOptions = options =>
        options.map(option => ({
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
  }, []);

  const fetchPatientData = useCallback(async () => {
    try {
      const token = localStorage.getItem('token');
      const headers = { Authorization: `Bearer ${token}` };

      const response = await axios.get(
        `http://localhost/api/patient/${patientId}`,
        { headers }
      );
      if (response.data) {
        const { id, patient_id, doctor_id, saved, ...patientDataWithoutIds } =
          response.data.patientData;
        setPatientData(patientDataWithoutIds);
      }
    } catch (error) {
      setError('Error fetching patient data.');
    }
  }, [patientId]);

  const fetchBisherigePatientTherapien = useCallback(async () => {
    try {
      const token = localStorage.getItem('token');
      const headers = { Authorization: `Bearer ${token}` };

      const response = await axios.get(
        `http://localhost/api/patient-bisherige-therapien/${patientId}`,
        { headers }
      );
      if (response.data.status === 'success') {
        setBisherigeTherapieValues({
          lokaleTherapie: response.data.data.lokaleTherapie,
          systemtherapie: response.data.data.systemtherapie,
        });
      }
    } catch (error) {
      setError('Error fetching patient bisherige therapien.');
    }
  }, [patientId]);

  const fetchAktuellePatientTherapien = useCallback(async () => {
    try {
      const token = localStorage.getItem('token');
      const headers = { Authorization: `Bearer ${token}` };

      const response = await axios.get(
        `http://localhost/api/patient-aktuelle-therapien/${patientId}`,
        { headers }
      );
      if (response.data.status === 'success') {
        setAktuelleTherapieValues({
          lokaleTherapie: response.data.data.lokaleTherapie,
          systemtherapie: response.data.data.systemtherapie,
        });
      }
    } catch (error) {
      setError('Error fetching patient aktuelle therapien.');
    }
  }, [patientId]);

  useEffect(() => {
    const updateTherapiesSelection = (therapyOptions, selectedIds) => {
      return therapyOptions.map(option => ({
        ...option,
        selected: selectedIds.includes(option.id),
      }));
    };

    if (bisherigeTherapieValues.lokaleTherapie.length > 0) {
      const selectedIds = extractTherapyIds(
        bisherigeTherapieValues.lokaleTherapie
      );
      setBisherigeTherapie(prevState => ({
        ...prevState,
        lokaleTherapie: updateTherapiesSelection(
          prevState.lokaleTherapie,
          selectedIds
        ),
      }));
    }

    if (bisherigeTherapieValues.systemtherapie.length > 0) {
      const selectedIds = extractTherapyIds(
        bisherigeTherapieValues.systemtherapie
      );
      setBisherigeTherapie(prevState => ({
        ...prevState,
        systemtherapie: updateTherapiesSelection(
          prevState.systemtherapie,
          selectedIds
        ),
      }));
    }

    if (aktuelleTherapieValues.lokaleTherapie.length > 0) {
      const selectedIds = extractTherapyIds(
        aktuelleTherapieValues.lokaleTherapie
      );
      setAktuelleTherapie(prevState => ({
        ...prevState,
        lokaleTherapie: updateTherapiesSelection(
          prevState.lokaleTherapie,
          selectedIds
        ),
      }));
    }

    if (aktuelleTherapieValues.systemtherapie.length > 0) {
      const selectedIds = extractTherapyIds(
        aktuelleTherapieValues.systemtherapie
      );
      setAktuelleTherapie(prevState => ({
        ...prevState,
        systemtherapie: updateTherapiesSelection(
          prevState.systemtherapie,
          selectedIds
        ),
      }));
    }
  }, [bisherigeTherapieValues, aktuelleTherapieValues]);

  useEffect(() => {
    const fetchData = async () => {
      setIsLoading(true);
      await fetchTherapyOptions();
      await fetchPatientData();
      await fetchBisherigePatientTherapien();
      await fetchAktuellePatientTherapien();
      setIsLoading(false);
    };

    fetchData();
  }, [
    patientId,
    fetchAktuellePatientTherapien,
    fetchBisherigePatientTherapien,
    fetchPatientData,
    fetchTherapyOptions,
  ]);

  const handleChange = e => {
    setPatientData({ ...patientData, [e.target.name]: e.target.value });
  };

  const handleCheckboxChange = (optionType, optionId, isAktuelle = false) => {
    const therapyOptionsToUpdate = isAktuelle
      ? aktuelleTherapie
      : bisherigeTherapie;
    const updatedTherapyOptions = { ...therapyOptionsToUpdate };

    updatedTherapyOptions[optionType] = updatedTherapyOptions[optionType].map(
      option => {
        if (option.id === optionId) {
          return { ...option, selected: !option.selected };
        }
        return option;
      }
    );

    if (isAktuelle) {
      setAktuelleTherapie(updatedTherapyOptions);
    } else {
      setBisherigeTherapie(updatedTherapyOptions);
    }
  };

  const handleBack = () => {
    navigate(-1);
  };

  const validateForm = () => {
    for (const [key, value] of Object.entries(patientData)) {
      if (
        [
          'bisherige_lokaltherapie_sonstiges',
          'bisherige_systemtherapie_sonstiges',
          'aktuelle_lokaltherapie_sonstiges',
          'aktuelle_systemtherapie_sonstiges',
        ].includes(key)
      ) {
        continue;
      }

      if (
        key === 'histopathologie_ergebnis' &&
        patientData.histopathologische_untersuchung !== 'Ja'
      ) {
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
    await axios.post(
      `http://localhost/api/${endpoint}/${patientId}`,
      therapieData,
      {
        headers: { Authorization: `Bearer ${token}` },
      }
    );
  };

  const handleSubmit = async e => {
    e.preventDefault();

    if (!validateForm()) {
      return;
    }

    const doctorId = localStorage.getItem('userId');
    const patientPayload = {
      ...patientData,
      doctor_id: doctorId,
      bisherige_lokaltherapie_sonstiges:
        bisherigeTherapieSonstiges.lokaleTherapie,
      bisherige_systemtherapie_sonstiges:
        bisherigeTherapieSonstiges.systemtherapie,
      aktuelle_lokaltherapie_sonstiges:
        aktuelleTherapieSonstiges.lokaleTherapie,
      aktuelle_systemtherapie_sonstiges:
        aktuelleTherapieSonstiges.systemtherapie,
    };

    try {
      const token = localStorage.getItem('token');
      await axios.put(
        `http://localhost/api/patient/${patientId}`,
        patientPayload,
        {
          headers: { Authorization: `Bearer ${token}` },
        }
      );
      console.log('asd');
      const processTherapieOptions = async (
        lokaleTherapie,
        systemtherapie,
        patientId,
        endpoint
      ) => {
        const lokaleTherapieIds = lokaleTherapie
          .filter(option => option.selected)
          .map(option => option.id);
        const systemtherapieIds = systemtherapie
          .filter(option => option.selected)
          .map(option => option.id);

        const therapieData = {
          lokaleTherapie: lokaleTherapieIds,
          systemtherapie: systemtherapieIds,
        };

        if (lokaleTherapieIds.length > 0 || systemtherapieIds.length > 0) {
          await updateTherapieData(patientId, therapieData, endpoint);
        }
      };

      await processTherapieOptions(
        bisherigeTherapie.lokaleTherapie,
        bisherigeTherapie.systemtherapie,
        patientId,
        'patient-bisherige-therapien'
      );
      await processTherapieOptions(
        aktuelleTherapie.lokaleTherapie,
        aktuelleTherapie.systemtherapie,
        patientId,
        'patient-aktuelle-therapien'
      );
      navigate('/dashboard');
    } catch (error) {
      setError(error.response?.data?.message || 'Ein Fehler ist aufgetreten.');
    }
  };

  if (isLoading) {
    return <div>Loading...</div>;
  }

  return (
    <>
      <NavBar />
      <div className="container mt-5">
        <h1>Patienten editieren</h1>
        {error && (
          <div className="alert alert-danger" role="alert">
            {error}
          </div>
        )}
        <form onSubmit={handleSubmit} className="mt-4">
          <div className="form-group mb-3">
            <label htmlFor="geburtsdatum">Geburtsdatum</label>
            <input
              type="date"
              className="form-control"
              id="geburtsdatum"
              name="geburtsdatum"
              value={patientData.geburtsdatum}
              onChange={handleChange}
            />
          </div>

          <div className="form-group mb-3">
            <label htmlFor="geschlecht">Geschlecht</label>
            <select
              className="form-control"
              id="geschlecht"
              name="geschlecht"
              value={patientData.geschlecht}
              onChange={handleChange}
            >
              <option value="">Geschlecht auswählen</option>
              <option value="Männlich">Männlich</option>
              <option value="Weiblich">Weiblich</option>
              <option value="Divers">Divers</option>
            </select>
          </div>

          <div className="form-group mb-3">
            <label htmlFor="ethnie">Ethnie</label>
            <input
              type="text"
              className="form-control"
              id="ethnie"
              name="ethnie"
              value={patientData.ethnie}
              onChange={handleChange}
            />
          </div>

          <div className="form-group mb-3">
            <label htmlFor="vermutete_diagnose">Vermutete Diagnose</label>
            <select
              className="form-control"
              id="vermutete_diagnose"
              name="vermutete_diagnose"
              value={patientData.vermutete_diagnose}
              onChange={handleChange}
            >
              <option value="">Diagnose auswählen</option>
              <option value="AD">AD</option>
              <option value="Psoriasis">Psoriasis</option>
              <option value="Flip-Flop">Flip-Flop</option>
            </select>
          </div>

          <div className="form-group mb-3">
            <label htmlFor="histopathologische_untersuchung">
              Histopathologische Untersuchung
            </label>
            <select
              className="form-control"
              id="histopathologische_untersuchung"
              name="histopathologische_untersuchung"
              value={patientData.histopathologische_untersuchung}
              onChange={handleChange}
            >
              <option value="">Option auswählen</option>
              <option value="Ja">Ja</option>
              <option value="Nein">Nein</option>
            </select>
          </div>

          {patientData.histopathologische_untersuchung === 'Ja' && (
            <div className="form-group mb-3">
              <label htmlFor="histopathologie_ergebnis">
                Histopathologie Ergebnis
              </label>
              <input
                className="form-control"
                id="histopathologie_ergebnis"
                name="histopathologie_ergebnis"
                value={patientData.histopathologie_ergebnis}
                onChange={handleChange}
              ></input>
            </div>
          )}

          <div className="form-group mb-3">
            <label htmlFor="bisherige_lokaltherapie">
              Bisherige lokale Therapie
            </label>
            {bisherigeTherapie.lokaleTherapie.map(option => (
              <div key={option.id} className="form-check">
                <input
                  className="form-check-input"
                  type="checkbox"
                  id={`bisherige_lokaltherapie_${option.id}`}
                  checked={option.selected}
                  onChange={() =>
                    handleCheckboxChange('lokaleTherapie', option.id)
                  }
                />
                <label
                  className="form-check-label"
                  htmlFor={`bisherige_lokaltherapie_${option.id}`}
                >
                  {option.option_name}
                </label>
              </div>
            ))}
            <div className="form-check">
              <input
                className="form-check-input"
                type="checkbox"
                id="bisherige_lokaltherapie_sonstiges_checkbox"
                onChange={e =>
                  setBisherigeTherapieSonstiges({
                    ...bisherigeTherapieSonstiges,
                    showLokale: e.target.checked,
                  })
                }
              />
              <label
                className="form-check-label"
                htmlFor="bisherige_lokaltherapie_sonstiges_checkbox"
              >
                Sonstiges
              </label>
            </div>
            {bisherigeTherapieSonstiges.showLokale && (
              <input
                type="text"
                className="form-control mt-2"
                placeholder="Sonstiges"
                value={bisherigeTherapieSonstiges.lokaleTherapie}
                onChange={e =>
                  setBisherigeTherapieSonstiges({
                    ...bisherigeTherapieSonstiges,
                    lokaleTherapie: e.target.value,
                  })
                }
              />
            )}
          </div>

          <div className="form-group mb-3">
            <label htmlFor="bisherige_systemtherapie">
              Bisherige systemische Therapie
            </label>
            {bisherigeTherapie.systemtherapie.map(option => (
              <div key={option.id} className="form-check">
                <input
                  className="form-check-input"
                  type="checkbox"
                  id={`bisherige_systemtherapie_${option.id}`}
                  checked={option.selected}
                  onChange={() =>
                    handleCheckboxChange('systemtherapie', option.id)
                  }
                />
                <label
                  className="form-check-label"
                  htmlFor={`bisherige_systemtherapie_${option.id}`}
                >
                  {option.option_name}
                </label>
              </div>
            ))}
            <div className="form-check">
              <input
                className="form-check-input"
                type="checkbox"
                id="bisherige_systemtherapie_sonstiges_checkbox"
                onChange={e =>
                  setBisherigeTherapieSonstiges({
                    ...bisherigeTherapieSonstiges,
                    showSystem: e.target.checked,
                  })
                }
              />
              <label
                className="form-check-label"
                htmlFor="bisherige_systemtherapie_sonstiges_checkbox"
              >
                Sonstiges
              </label>
            </div>
            {bisherigeTherapieSonstiges.showSystem && (
              <input
                type="text"
                className="form-control mt-2"
                placeholder="Sonstiges"
                value={bisherigeTherapieSonstiges.systemtherapie}
                onChange={e =>
                  setBisherigeTherapieSonstiges({
                    ...bisherigeTherapieSonstiges,
                    systemtherapie: e.target.value,
                  })
                }
              />
            )}
          </div>

          <div className="form-group mb-3">
            <label htmlFor="aktuelle_lokaltherapie">
              Aktuelle lokale Therapie
            </label>
            {aktuelleTherapie.lokaleTherapie.map(option => (
              <div key={option.id} className="form-check">
                <input
                  className="form-check-input"
                  type="checkbox"
                  id={`aktuelle_lokaltherapie_${option.id}`}
                  checked={option.selected}
                  onChange={() =>
                    handleCheckboxChange('lokaleTherapie', option.id, true)
                  }
                />
                <label
                  className="form-check-label"
                  htmlFor={`aktuelle_lokaltherapie_${option.id}`}
                >
                  {option.option_name}
                </label>
              </div>
            ))}
            <div className="form-check">
              <input
                className="form-check-input"
                type="checkbox"
                id="aktuelle_lokaltherapie_sonstiges_checkbox"
                onChange={e =>
                  setAktuelleTherapieSonstiges({
                    ...aktuelleTherapieSonstiges,
                    showLokale: e.target.checked,
                  })
                }
              />
              <label
                className="form-check-label"
                htmlFor="aktuelle_lokaltherapie_sonstiges_checkbox"
              >
                Sonstiges
              </label>
            </div>
            {aktuelleTherapieSonstiges.showLokale && (
              <input
                type="text"
                className="form-control mt-2"
                placeholder="Sonstiges"
                value={aktuelleTherapieSonstiges.lokaleTherapie}
                onChange={e =>
                  setAktuelleTherapieSonstiges({
                    ...aktuelleTherapieSonstiges,
                    lokaleTherapie: e.target.value,
                  })
                }
              />
            )}
          </div>

          <div className="form-group mb-3">
            <label htmlFor="aktuelle_systemtherapie">
              Aktuelle systemische Therapie
            </label>
            {aktuelleTherapie.systemtherapie.map(option => (
              <div key={option.id} className="form-check">
                <input
                  className="form-check-input"
                  type="checkbox"
                  id={`aktuelle_systemtherapie_${option.id}`}
                  checked={option.selected}
                  onChange={() =>
                    handleCheckboxChange('systemtherapie', option.id, true)
                  }
                />
                <label
                  className="form-check-label"
                  htmlFor={`aktuelle_systemtherapie_${option.id}`}
                >
                  {option.option_name}
                </label>
              </div>
            ))}
            <div className="form-check">
              <input
                className="form-check-input"
                type="checkbox"
                id="aktuelle_systemtherapie_sonstiges_checkbox"
                onChange={e =>
                  setAktuelleTherapieSonstiges({
                    ...aktuelleTherapieSonstiges,
                    showSystem: e.target.checked,
                  })
                }
              />
              <label
                className="form-check-label"
                htmlFor="aktuelle_systemtherapie_sonstiges_checkbox"
              >
                Sonstiges
              </label>
            </div>
            {aktuelleTherapieSonstiges.showSystem && (
              <input
                type="text"
                className="form-control mt-2"
                placeholder="Sonstiges"
                value={aktuelleTherapieSonstiges.systemtherapie}
                onChange={e =>
                  setAktuelleTherapieSonstiges({
                    ...aktuelleTherapieSonstiges,
                    systemtherapie: e.target.value,
                  })
                }
              />
            )}
          </div>

          <div className="form-group mb-3">
            <label htmlFor="jucken_letzte_24_stunden">
              Jucken in den letzten 24 Stunden
            </label>
            <select
              className="form-control"
              id="jucken_letzte_24_stunden"
              name="jucken_letzte_24_stunden"
              value={patientData.jucken_letzte_24_stunden}
              onChange={handleChange}
            >
              <option value="">Intensität auswählen</option>
              {[...Array(11).keys()].map(num => (
                <option key={num} value={num}>
                  {num === 0
                    ? '0 (gar kein Jucken)'
                    : num === 10
                    ? '10 (schwerstes vorstellbares Jucken)'
                    : num}
                </option>
              ))}
            </select>
          </div>

          <button type="submit" className="btn btn-link mb-3">
            Patientendaten Speichern
          </button>
          <br />
          <button
            type="button"
            className="btn btn-link mb-3"
            onClick={handleBack}
          >
            Zurück
          </button>
        </form>
      </div>
    </>
  );
};

export default PatientForm;