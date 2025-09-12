import { useState, useEffect } from 'react';
import { LandingPage } from './components/LandingPage';
import { Dashboard } from './components/Dashboard';
import { AdminDashboard } from './components/AdminDashboard';
import { AuthProvider, useAuth } from './components/AuthContext';
import { Toaster } from 'sonner@2.0.3';

function AppContent() {
  const { user, isLoading } = useAuth();
  const [currentPage, setCurrentPage] = useState<'landing' | 'dashboard'>('landing');

  useEffect(() => {
    if (user) {
      setCurrentPage('dashboard');
    } else {
      setCurrentPage('landing');
    }
  }, [user]);

  if (isLoading) {
    return (
      <div className="size-full flex items-center justify-center">
        <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
      </div>
    );
  }

  return (
    <div className="size-full">
      {currentPage === 'landing' ? (
        <LandingPage onNavigateToDashboard={() => setCurrentPage('dashboard')} />
      ) : user?.role === 'admin' ? (
        <AdminDashboard onNavigateToLanding={() => setCurrentPage('landing')} />
      ) : (
        <Dashboard onNavigateToLanding={() => setCurrentPage('landing')} />
      )}
    </div>
  );
}

export default function App() {
  return (
    <AuthProvider>
      <AppContent />
      <Toaster 
        position="top-right"
        richColors
        closeButton
        style={{
          '--normal-bg': 'var(--popover)',
          '--normal-text': 'var(--popover-foreground)',
          '--normal-border': 'var(--border)',
        } as React.CSSProperties}
      />
    </AuthProvider>
  );
}