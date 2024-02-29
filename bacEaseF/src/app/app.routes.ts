import { RouterModule, Routes } from '@angular/router';
import { AccueilComponent } from './accueil/accueil.component';
import { TestComponent } from './test/test.component';
import { NgModule } from '@angular/core';

export const routes: Routes = [
  { path: '', redirectTo: '/accueil', pathMatch: 'full' }, // Rediriger vers '/accueil' par défaut
  { path: 'accueil', component: AccueilComponent },
  { path: 'test', component: TestComponent }, // Définissez le chemin et associez-le au composant TestComponent

  // Ajoutez d'autres routes pour les autres fonctionnalités de votre application
];
@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }