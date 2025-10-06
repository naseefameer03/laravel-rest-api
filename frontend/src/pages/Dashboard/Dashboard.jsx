import { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import api from "../../api/axios";

export default function Dashboard() {
  const [user, setUser] = useState(null);
  const navigate = useNavigate();

  useEffect(() => {
    api.get("/user")
      .then(res => setUser(res.data))
      .catch(() => navigate("/login"));
  }, []);

  const logout = async () => {
    await api.post("/logout");
    localStorage.removeItem("token");
    navigate("/login");
  };

  return (
    <div>
      <h2>Dashboard</h2>
      {user ? <p>Welcome, {user.name}</p> : <p>Loading...</p>}
      <button onClick={logout}>Logout</button>
    </div>
  );
}
