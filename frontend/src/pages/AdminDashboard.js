import React from 'react';
import axios from 'axios';
import { saveAs } from 'file-saver';

const AdminDashboard = () => {
  const handleExport = async () => {
    const adminToken = localStorage.getItem('adminToken');

    try {
      const response = await axios.get('http://localhost/api/admin/export', {
        headers: {
          Authorization: `Bearer ${adminToken}`,
        },
        responseType: 'blob',
      });

      const utf8BOM = new Uint8Array([0xef, 0xbb, 0xbf]);
      const blob = new Blob([utf8BOM, response.data], {
        type: 'text/csv;charset=utf-8',
      });
      saveAs(blob, 'patienten_daten.csv');
    } catch (error) {
      console.error('Error during export:', error);
    }
  };

  return (
    <div
      className="d-flex justify-content-center align-items-center"
      style={{ height: '100vh' }}
    >
      <button onClick={handleExport} className="btn btn-link link custom-link">
        Patientendaten exportieren
      </button>
    </div>
  );
};

export default AdminDashboard;
